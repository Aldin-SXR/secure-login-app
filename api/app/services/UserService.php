<?php
use Firebase\JWT\JWT;

class UserService {
    /** @var UserDao User data access object. */
    private $user_dao;

    public function __construct() {
        $this->user_dao = new UserDao();
    }

    /**
     * Check user upon login.
     */
    public function log_in($data) {
        /* Log system access */
        // $sys_access_dao = new SystemAccessDao();
        // $sys_access_dao->log_access();

        $allowed_fields = [ 'username', 'password', 'captcha_response' ];
        $required_fields = [ 'username', 'password' ];
        $parsed_data = Validator::validate_data($data, $allowed_fields, $required_fields);
        
        /* Handle captcha response */
        if (array_key_exists('captcha_response', $parsed_data) && isset($parsed_data['captcha_response'])) {
            $response = ReCaptcha::validate($parsed_data['captcha_response']);
            if (!$response['success']) {
                JsonResponse::error('Incorrect captcha verification.');
            }
        }

        /* Attempt to fetch a user */
        $user = $this->user_dao->get_user_by_credentials($parsed_data['username']);
        /* Handle non-existing user */
        if (!$user) {
            JsonResponse::error('Provided account credentials are invalid.', 401);
        }

        /* Verify password */
        if (!password_verify($parsed_data['password'], $user['password'])) {
            JsonResponse::error('Provided account credentials are invalid.', 401);
        }

        /* Generate temporary login hash */
        $login_hash = sha1(Util::random_str(16));
        $expiry = strtotime('+30 seconds');
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
        $expiry = strtotime('+30 seconds');
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
        $allowed_fields = [ 'login_hash', 'auth_type', 'auth_code' ];
        $required_fields = $allowed_fields;
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
            default:
                JsonResponse::error('Invalid login attempt.');
        }

        /* Send the login JWT */
        if ($parsed_data['auth_code'] == $code) {
            /* Generate login token */
            $token = [
                'data' => [
                    'id' => $auth_data['id'],
                    'email_address' => $auth_data['email_address']
                ],
                'iat' => time(),
                'exp' => strtotime('+60 minutes')
            ];

            JsonResponse::output([
                'jwt' => JWT::encode($token, JWT_SECRET)
            ], 'Successfully logged in.');
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
}