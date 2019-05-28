<?php
use Firebase\JWT\JWT;

class UserService {
    /** @var UserDao User data access object. */
    private $user_dao;
    /** @var SystemAccessDao System data access object. */
    private $sys_access_dao;
    /** @var RecoveryTokenDao Recovery token data access object. */
    private $recovery_dao;

    public function __construct() {
        $this->user_dao = new UserDao();
        $this->sys_access_dao = new SystemAccessDao();
        $this->recovery_dao = new RecoveryTokenDao();
    }

    /**
     * Check user upon login.
     */
    public function log_in($data) {
        /* Log system access */
        $this->sys_access_dao->log_access();

        $allowed_fields = [ 'username', 'password', 'captcha_response' ];
        $required_fields = [ 'username', 'password' ];

        /* Get the amount of login attempts (and make captcha mandatory, if necessary) */
        $attempt = $this->sys_access_dao->get_access_attempts();
        if ($attempt['failed_attempts'] >= 5) {
            $required_fields[ ] = 'captcha_response';
        }
        $parsed_data = Validator::validate_data($data, $allowed_fields, $required_fields);
        
        /* Handle captcha response */
        if (array_key_exists('captcha_response', $parsed_data) && isset($parsed_data['captcha_response'])) {
            $response = ReCaptcha::validate($parsed_data['captcha_response']);
            if (!$response['success']) {
                $this->sys_access_dao->update_access_attempt('failed');
                JsonResponse::error('Incorrect captcha verification.');
            }
        }

        /* Attempt to fetch a user */
        $user = $this->user_dao->get_user_by_credentials($parsed_data['username']);
        /* Handle non-existing user */
        if (!$user) {
            $this->sys_access_dao->update_access_attempt('failed');
            JsonResponse::error('Provided account credentials are invalid.', 401);
        }

        /* Verify password */
        if (!password_verify($parsed_data['password'], $user['password'])) {
            $this->sys_access_dao->update_access_attempt('failed');
            JsonResponse::error('Provided account credentials are invalid.', 401);
        }

        /* See if 'Remember Me' timestamp has been stored in the database (bypass authorization) */
        if (strtotime($user['remember_me_until']) > time()) {
            $this->output_jwt($user);
        }

        /* Generate temporary login hash */
        $login_hash = sha1(Util::random_str(16));
        $expiry = strtotime(LOGIN_EXPIRY);
        $this->user_dao->set_login_hash($user['id'], $login_hash, date('Y-m-d H:i:s', $expiry));
        
        $auth = [
            'user_id' => $user['id'],
            'login_hash' => $login_hash,
            'expiry' => $expiry
        ];
        JsonResponse::output($auth, 'User successfully validated.');
    }

    public function send_sms_code($data) {
        $allowed_fields = [ 'login_hash' ];
        $required_fields = $allowed_fields;
        $parsed_data = Validator::validate_data($data, $allowed_fields, $required_fields);

        /* Send an SMS with the authentication code */
        $auth_data = $this->user_dao->get_phone_number($parsed_data['login_hash']);
        $code = Util::random_str(6, '0123456789');
        $expiry = strtotime(SMS_EXPIRY);
        SendSms::send_message(
            'Your one-time authentication code is: '.$code."\n", 
            $auth_data['phone_number'], 'SSSD Login');
        
        /* Store the authentication code */
        $this->user_dao->set_sms_code($auth_data['id'], $code, date('Y-m-d H:i:s', $expiry));
        JsonResponse::output([
            'user_id' => $auth_data['id'],
            'sms_code' => $code,
            'expiry' => $expiry
        ], 'Successfully sent the authentication code.');
    }

    /* Verify authentication method */
    public function verify_authentication($data) {
        // TODO: Should OPT fails count towards captcha?
        $allowed_fields = [ 'login_hash', 'auth_type', 'auth_code', 'remember_me' ];
        $required_fields = [ 'login_hash', 'auth_type', 'auth_code' ];
        $parsed_data = Validator::validate_data($data, $allowed_fields, $required_fields);

        $auth_data = $this->user_dao->get_by_login_hash($parsed_data['login_hash'], $parsed_data['auth_type']);
        if (!$auth_data) {
            JsonResponse::error('Invalid login attempt.');
        }
        /* Expired login token */
        if (strtotime($auth_data['login_hash_expiry']) < time()) {
            JsonResponse::error('Your login attempt has expired. Please try again.');
        }

        /* Verify authentication code */
        switch ($parsed_data['auth_type']) {
            case 'otp':
                $otp = new OTPGenerator($auth_data['otp_secret'], $auth_data['email_address']);
                $code = $otp->get_otp();
                break;
            case 'sms':
                /* Check for expired SMS login code */
                if (strtotime($auth_data['sms_code_expiry']) < time()) {
                    JsonResponse::error('Your verification code has expired.');
                }
                $code = $auth_data['sms_code'];
                break;
            case 'fido':
                $u2f = new U2FAuthentication();
                /* Compare saved OTP vs received one */
                $yubiko_id_set = isset($auth_data['yubiko_id']);
                if ($yubiko_id_set && (substr($parsed_data['auth_code'], 0, 12) !== $auth_data['yubiko_id'])) {
                    JsonResponse::error('Your U2F authentication attempt is invalid.');
                }
                /* Validate Yubiko OTP */
                if (!$u2f->validate_otp($parsed_data['auth_code'])) {
                    JsonResponse::error('Your U2F authentication attempt is invalid.');
                }
                /* Check if the ID is registered in the database */
                if (!$yubiko_id_set) {
                    $this->user_dao->set_yubiko_id($auth_data['id'], substr($parsed_data['auth_code'], 0, 12));
                } 
                $code = $parsed_data['auth_code'];
                break;
            default:
                JsonResponse::error('Invalid login attempt.');
        }

        /* Send the login JWT */
        if ($parsed_data['auth_code'] == $code) {
            /* See if 'Remember Me' token is set in the request (bypass authorization). */
            if (isset($parsed_data['remember_me']) && $parsed_data['remember_me']) {
                $this->user_dao->set_remember_me($auth_data['id'], date('Y-m-d H:i:s', strtotime(REMEMBER_ME_EXPIRY)));
            }
            $this->output_jwt($auth_data);
        } else {
            JsonResponse::error('Invalid authentication code provided.', 401);
        }
    }

    /**
     * Register a new user.
     */
    public function register($data) {
        $allowed_fields = [ 'name', 'user_name', 'email_address', 'email_address', 'phone_number', 'password' ];
        $required_fields = $allowed_fields;
        $parsed_data = Validator::validate_data($data, $allowed_fields, $required_fields);

        /** Field validators */
        /* Check uniqueness of username and e-mail address */
        $users = $this->user_dao->get_by_email_address_or_username($parsed_data['email_address'], $parsed_data['user_name']);
        foreach ($users as $user) {
            /* Validate unique email */
            if ($user['email_address'] === $parsed_data['email_address']) {
                JsonResponse::error('This e-mail address is already taken.');
            }
            /* Validate unique username */
            if ($user['user_name'] === $parsed_data['user_name']) {
                JsonResponse::error('This username is already taken.');
            }
        }

        /* E-mail (valid) */
        Validator::validate_email($parsed_data['email_address']);
        /* Username (no special characters) */
        Validator::validate_username($parsed_data['user_name']);
        /* Password (length, complexity, was it breached) */
        Validator::validate_password($parsed_data['password']);
        $parsed_data['password'] = password_hash($parsed_data['password'], PASSWORD_DEFAULT);

        /* Set up Google OTP */
        $secret = Util::random_str(16);
        $otp = new OTPGenerator($secret, $parsed_data['email_address']);
        $otp_link = $otp->get_provisioning_link();
        $parsed_data['otp_secret'] = $secret;

        /** Insert the new account */
        $this->user_dao->insert_user($parsed_data);
        JsonResponse::output([
            'otp_qr' => $otp_link
        ], 'Successful registration.');
    }

    private function output_jwt($user) {
        $this->sys_access_dao->update_access_attempt('success');
        /* Generate login token */
        $token = [
            'data' => [
                'id' => $user['id'],
                'email_address' => $user['email_address']
            ],
            'iat' => time(),
            'exp' => strtotime(JWT_EXPIRY)
        ];

        JsonResponse::output([
            'jwt' => JWT::encode($token, JWT_SECRET)
        ], 'Successfully logged in.');
    }

    /**
     * Password recovery.
     */

    /* Initiate the recovery procedure */
    public function initiate_recovery($data) {
        $allowed_fields = [ 'email_address' ];
        $required_fields = $allowed_fields;
        $parsed_data = Validator::validate_data($data, $allowed_fields, $required_fields);

        /* Check if user with the given e-mail address exists */
        $user = $this->user_dao->get_by_email_address($parsed_data['email_address']);
        /* Handle non-existing user */
        if (!$user) {
            JsonResponse::error('Provided account credentials are invalid.', 401);
        }

        /* Invalidate any previous tokens */
        $this->recovery_dao->invalidate_last_token($user['id']);

        /* Insert recovery token */
        $token = sha1(Util::random_str(16).time());
        $this->recovery_dao->set_recovery_token($user['id'], $token);
        
        /* Send recovery token */
        Util::send_mail('password_recovery', $user, $token);

        JsonResponse::output(NULL, 'Recovery token sent. It will be valid for 5 minutes.');
    }

    /* Validate the recovery token */
    public function validate_token($data) {
        $allowed_fields = [ 'recovery_token' ];
        $required_fields = $allowed_fields;
        $parsed_data = Validator::validate_data($data, $allowed_fields, $required_fields); 

        /* Get token data and see if it is valid */
        $token = $this->recovery_dao->get_token_data($parsed_data['recovery_token']);
        $this->check_token_validity($token);

        JsonResponse::output(NULL, 'Recovery token is valid.');
    }

    /* Set new account password */
    public function reset_password($data) {
        $allowed_fields = [ 'recovery_token', 'password' ];
        $required_fields = $allowed_fields;
        $parsed_data = Validator::validate_data($data, $allowed_fields, $required_fields); 

        /* Get token data and see if it is valid */
        $token = $this->recovery_dao->get_token_data($parsed_data['recovery_token']);
        $this->check_token_validity($token);

        /* Check new password validity */
        Validator::validate_password($parsed_data['password']);
        $parsed_data['password'] = password_hash($parsed_data['password'], PASSWORD_DEFAULT);

        /* Update the password */
        $this->user_dao->update_password($token['user_id'], $parsed_data['password']);
        JsonResponse::output(NULL, 'Password successfully reset.');
    }

    private function check_token_validity($token) {
        /* Check for non-existent tokens */
        if (!$token) {
            JsonResponse::error('Recovery token is invalid.');
        }
        /** Check for invalidated tokens */
        if (!$token['valid']) {
            JsonResponse::error('Recovery token is invalid.');
        }
        /* Check for expired tokens */
        if (strtotime($token['expires_at']) < time()) {
            $this->recovery_dao->invalidate_last_token($token['user_id']);
            JsonResponse::error('Recovery token has expired.');
        }
    }
}