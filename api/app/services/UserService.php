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
        $allowed_fields = [ 'username', 'password', 'captcha_response' ];
        $required_fields = [ 'username', 'password' ];
        $parsed_data = Validator::validate_data($data, $allowed_fields, $required_fields);
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
        /* Generate login token */
        $token = [
            'data' => [
                'id' => $user['id'],
                'user_name' => $user['user_name'],
                'email_address' => $user['email_address']
            ],
            'iat' => time(),
            'exp' => strtotime('+60 minutes')
        ];

        $auth = [ 
            'jwt' => JWT::encode($token, JWT_SECRET)
        ];
        JsonResponse::output($auth, 'Successfully logged in.');
    }
}