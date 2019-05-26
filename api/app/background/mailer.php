<?php

require_once __DIR__.'/autoload.php';

$type = $argv[1];

switch ($type) {
    case 'password_recovery':
        $user = json_decode($argv[2], true);
        $token = $argv[3];
        $mailer = new Mailer();
        $mailer->send_recovery_token($user, $token);
        break;
    default:
        break;
}