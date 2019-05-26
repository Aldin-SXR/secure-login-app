<?php

class RecoveryTokenDao extends BaseDao {

    public function set_recovery_token($id, $recovery_token) {
        $stmt = $this->pdo->prepare('INSERT INTO recovery_tokens(token, issued_at, expires_at, user_id)
            VALUES (:token, :issued_at, :expires_at, :user_id)');
        $stmt->execute([
            'token' => $recovery_token,
            'issued_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
            'user_id' => $id
        ]);
        return $this->pdo->lastInsertId();
    }

    public function get_token_data($token) {
        $stmt = $this->pdo->prepare('SELECT * FROM recovery_tokens WHERE token = :token;');
        $stmt->execute([
            'token' => $token
        ]);
        $token = $stmt->fetch();
        return $token;
    }
}