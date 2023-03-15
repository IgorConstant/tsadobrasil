<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class Mail
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function send(array $payload, string $template, string $subject)
    {
        $mail = new PHPMailer(true);

        try {

            $mail->IsSMTP(); // Define que a mensagem ser� SMTP
            $mail->Host = "smtp.gmail.com"; // Endere�o do servidor SMTP
            $mail->Port = 587;
            $mail->SMTPSecure = 'tls';
            $mail->SMTPAuth = true; // Usa autentica��o SMTP? (opcional)
            $mail->Username = ''; // Usu�rio do servidor SMTP
            $mail->Password = ''; // Senha do servidor SMTP
            $mail->SMTPDebug = 0;
            
            // Define o remetente
            $mail->From = 'webmaster.duettoag@gmail.com'; // Seu e-mail

            $mail->FromName = $this->container->appName; // Seu nome

            $mail->setFrom($this->container->appEmail, $this->container->appName);

            $mail->addAddress($payload['to_email'], $payload['to_name']);

            $mail->isHTML(true);
            $mail->Subject = utf8_decode($subject);

            if($payload['arquivo_name']){
                $mail->AddAttachment($payload['arquivo_temp'], $payload['arquivo_name']);
            }

            $mail->Body = $this->container->view->fetch('mails/'.$template, $payload);

            $mail->send();
        } catch (PHPMailerException $e) {
            echo 'Houve um erro na tentativa de enviar um e-mail';
        }
    }
}
