<?php

class SystemAccessDao extends BaseDao {

    public function log_access() {
        $stmt = $this->pdo->prepare('INSERT INTO login_attempts(ip_address, user_agent, last_accessed)
            VALUES(:ip_address, :user_agent, :last_accessed);');
        $stmt->execute([
            'ip_address' => isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : 
                                        isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'unknown',
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown',
            'last_accessed' => date('Y-m-d H:i:s')
        ]);
        return $this->pdo->lastInsertId();
    }

    public function update_acess() {

    }
}