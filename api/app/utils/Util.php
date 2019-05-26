<?php
/**
 * Various utility files.
 */

class Util {
    public static function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    public static function get_ip_address() {
        return isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : 
                    isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'unknown';
    }

    public static function get_user_agent() {
        return  isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown';
    }

    public static function send_mail(...$args) {
        $args_string = '';
        foreach ($args as &$arg) {
            if (is_array($arg)) {
                $args_string .= ' '.escapeshellarg(json_encode($arg));
            } else {
                $args_string .= ' '.$arg;
            }
        }
        $command = 'php '.__DIR__.'/../background/mailer.php '.$args_string.' > /dev/null &';
        exec($command);
    }
}