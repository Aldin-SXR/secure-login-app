<?php

class SystemAccessDao extends BaseDao {

    public function log_access() {
        $stmt = $this->pdo->prepare(
            'INSERT INTO login_attempts(ip_address, user_agent, last_accessed, failed_attempts)
            VALUES(:ip_address, :user_agent, :last_accessed, :failed_attempts)
            ON DUPLICATE KEY UPDATE last_accessed = :la_update;'
        );
        $stmt->execute([
            'ip_address' => Util::get_ip_address(),
            'user_agent' => Util::get_user_agent(),
            'last_accessed' => date('Y-m-d H:i:s'),
            'failed_attempts' => 0,
            'la_update' => date('Y-m-d H:i:s')
        ]);
        return $this->pdo->lastInsertId();
    }

    public function update_access_attempt($type) {
        switch ($type) {
            case 'failed':
                $sql = 'UPDATE login_attempts SET failed_attempts = failed_attempts + 1 WHERE ip_address = :ip and user_agent = :ua;';
                break;
            case 'success':
                $sql = 'UPDATE login_attempts SET failed_attempts = 0 WHERE ip_address = :ip and user_agent = :ua;'; 
                break;
            default:
                return NULL;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'ip' => Util::get_ip_address(),
            'ua' => Util::get_user_agent()
        ]);
    }

    public function get_access_attempts() {
        $stmt = $this->pdo->prepare('SELECT failed_attempts FROM login_attempts WHERE ip_address = :ip AND user_agent = :ua;');
        $stmt->execute([
            'ip' => Util::get_ip_address(),
            'ua' => Util::get_user_agent()
        ]);
        $attempt = $stmt->fetch();
        return $attempt;
    }
}