<?php

class Mailer {
    /** @var Swift_Mailer $mailer Mailer client. */
    private $mailer;

    public function __construct() {
        $transport = (new Swift_SmtpTransport(SMTP_SERVER, SMTP_PORT, 'tls'))
                                ->setUsername(SMTP_USER)
                                ->setPassword(SMTP_PASS);
        $this->mailer = new Swift_Mailer($transport);
    }

    public function send_recovery_token($user, $token) {
        $message = file_get_contents(__DIR__.'/../templates/recover_password_template.html');
        $message = str_replace('{{recovery_url}}', FRONTEND_CLIENT.'/recover/'.$token, $message);
        $message = (new Swift_Message())
                                ->setSubject('[SSSD] Recover your password')
                                ->setFrom([ 'admin@sssd-project.com' => 'SSSD Administrator' ])
                                ->setTo([ $user['email_address'] => $user['name'] ])
                                ->setBody($message, 'text/html');
        try {
            $this->mailer->send($message);
        } catch (\Throwable $e) {
            print_r($e->getMessage());
        }
    }
}