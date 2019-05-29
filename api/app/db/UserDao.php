<?php

class UserDao extends BaseDao {

    public function get_user_by_credentials($credentials) {
        /* Check whether a username or an email was provided. */
        $stmt = $this->pdo->prepare('SELECT id, user_name, email_address, password, remember_me_until 
                                                            FROM users WHERE user_name = :user_name OR email_address = :email_address;');
        $stmt->execute([
            'user_name' => $credentials,
            'email_address' => $credentials
        ]);
        $user = $stmt->fetch();
        return $user;
    }

    public function insert_user($user) {
        $stmt = $this->pdo->prepare('INSERT INTO users(name, user_name, email_address, phone_number, password, otp_secret)
            VALUES (:name, :user_name, :email_address, :phone_number, :password, :otp_secret);');
        $stmt->execute($user);
        return $this->pdo->lastInsertId();
    }

    public function set_login_hash($id, $hash, $expiry) {
        $stmt = $this->pdo->prepare('INSERT INTO login_hashes (login_hash, issued_at, login_hash_expiry, user_id, valid) 
            VALUES (:login_hash, :issued_at, :login_hash_expiry, :user_id, :valid);');
        $stmt->execute([
            'user_id' => $id,
            'login_hash' => $hash,
            'issued_at' => date('Y-m-d H:i:s'),
            'login_hash_expiry' => $expiry,
            'valid' => 1
        ]);
    }

    public function set_sms_code($id, $code, $expiry) {
        $stmt = $this->pdo->prepare('INSERT INTO validation_codes (sms_code, issued_at, sms_code_expiry, user_id, valid)
            VALUES (:sms_code, :issued_at, :sms_code_expiry, :user_id, :valid);');
        $stmt->execute([
            'user_id' => $id,
            'sms_code' => $code,
            'issued_at' => date('Y-m-d H:i:s'),
            'sms_code_expiry' => $expiry,
            'valid' => 1
        ]);
    }

    public function get_by_login_hash($hash, $type) {
        switch ($type) {
            case 'otp':
                $sql ='SELECT u.id, u.email_address, lh.login_hash, lh.login_hash_expiry, u.otp_secret FROM users AS u
                            JOIN login_hashes AS lh ON u.id = lh.user_id
                            WHERE lh.login_hash = :hash AND lh.valid = 1;';
                break;
            case 'sms':
                $sql ='SELECT u.id, u.email_address, lh.login_hash, lh.login_hash_expiry, vc.sms_code, vc.sms_code_expiry FROM users AS u
                            JOIN login_hashes AS lh ON u.id = lh.user_id
                            JOIN validation_codes AS vc on u.id = vc.user_id
                            WHERE lh.login_hash = :hash AND lh.valid = 1 ORDER BY vc.issued_at DESC LIMIT 1;';
                break;
            case 'fido':
                $sql ='SELECT u.id, u.email_address, lh.login_hash, lh.login_hash_expiry, u.yubiko_id FROM users AS u 
                            JOIN login_hashes AS lh ON u.id = lh.user_id
                            WHERE lh.login_hash = :hash AND lh.valid = 1;';
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
        $stmt = $this->pdo->prepare('SELECT u.id, u.phone_number FROM users AS u 
            JOIN login_hashes AS lh ON u.id = lh.user_id WHERE lh.login_hash = :hash AND lh.valid = 1;');
        $stmt->execute([
            'hash' => $hash
        ]);
        $data = $stmt->fetch();
        return $data;
    }

    public function get_by_email_address_or_username($email, $username) {
        $stmt = $this->pdo->prepare('SELECT email_address, user_name FROM users WHERE email_address = :email_address OR user_name = :user_name;');
        $stmt->execute([
            'email_address' => $email,
            'user_name' => $username
        ]);
        $user = $stmt->fetchAll();
        return $user;
    }

    public function get_by_email_address($email) {
        $stmt = $this->pdo->prepare('SELECT id, email_address, name FROM users WHERE email_address = :email_address;');
        $stmt->execute([
            'email_address' => $email
        ]);
        $user = $stmt->fetch();
        return $user;
    }

    public function set_remember_me($id, $remember_me_until) {
        $stmt = $this->pdo->prepare('UPDATE users SET remember_me_until = :remember_me_until WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'remember_me_until' => $remember_me_until
        ]);
    }

    public function update_password($id, $password) {
        $stmt = $this->pdo->prepare('UPDATE users SET password = :password WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'password' => $password
        ]);
    }

    public function set_yubiko_id($id, $yubiko_id) {
        $stmt = $this->pdo->prepare('UPDATE users SET yubiko_id = :password WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'password' => $yubiko_id
        ]);
    }

    public function invalidate_last_login_hash($id) {
        $stmt = $this->pdo->prepare('UPDATE login_hashes SET valid = 0 WHERE user_id = :user_id
            ORDER BY issued_at DESC LIMIT 1;');
        $stmt->execute([
           'user_id' => $id 
        ]);
    }

    public function invalidate_last_sms_code($id) {
        $stmt = $this->pdo->prepare('UPDATE validation_codes SET valid = 0 WHERE user_id = :user_id
            ORDER BY issued_at DESC LIMIT 1;');
        $stmt->execute([
           'user_id' => $id 
        ]);
    }
}
