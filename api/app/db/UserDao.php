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

    public function insert_user($user) {
        $stmt = $this->pdo->prepare('INSERT INTO users(name, user_name, email_address, phone_numer, password)
            VALUES (:name, :user_name, :email_address, :phone_number, :password);');
        $stmt->execute($user);
        return $this->pdo->lastInsertId();
    }

    public function set_login_hash($id, $hash, $expiry) {
        $stmt = $this->pdo->prepare('UPDATE users SET login_hash = :login_hash, login_hash_expiry = :login_hash_expiry WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'login_hash' => $hash,
            'login_hash_expiry' => $expiry
        ]);
    }

    public function set_sms_code($id, $code, $expiry) {
        $stmt = $this->pdo->prepare('UPDATE users SET sms_code = :sms_code, sms_code_expiry = :sms_code_expiry WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'sms_code' => $code,
            'sms_code_expiry' => $expiry
        ]);
    }

    public function get_by_login_hash($hash, $type) {
        switch ($type) {
            case 'otp':
                $sql ='SELECT id, email_address, login_hash, login_hash_expiry, otp_secret FROM users WHERE login_hash = :hash;';
                break;
            case 'sms':
                $sql ='SELECT id, email_address, login_hash, login_hash_expiry, sms_code, sms_code_expiry FROM users WHERE login_hash = :hash;';
                break;
            default:
                return NULL;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'hash' => $hash
        ]);
        $data = $stmt->fetch();
        return $data;
    }

    public function get_phone_number($hash) {
        $stmt = $this->pdo->prepare('SELECT id, phone_number FROM users WHERE login_hash = :hash');
        $stmt->execute([
            'hash' => $hash
        ]);
        $data = $stmt->fetch();
        return $data;
    }
}