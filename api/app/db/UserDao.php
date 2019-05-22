<?php

class UserDao extends BaseDao {

    public function get_user_by_credentials($credentials) {
        /* Check whether a username or an email was provided. */
        $stmt = $this->pdo->prepare('SELECT id, user_name, email_address, password FROM users WHERE user_name = :user_name OR email_address = :email_address;');
        $stmt->execute([
            'user_name' => $credentials,
            'email_address' => $credentials
        ]);
        $user = $stmt->fetch();
        return $user;
    }
}
