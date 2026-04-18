<?php

namespace Api;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        $this->mailer->isSMTP();
        $this->mailer->Host       = $_ENV['MAIL_HOST'];
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $_ENV['MAIL_USER'];
        $this->mailer->Password   = $_ENV['MAIL_PASS'];
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port       = (int) $_ENV['MAIL_PORT'];
        $this->mailer->setFrom($_ENV['MAIL_FROM'], 'Contact Form');
    }

    public function sendConfirmation(ContactDTO $dto): void
    {
        $this->mailer->clearAddresses();
        $this->mailer->addAddress($dto->email, $dto->name);
        $this->mailer->isHTML(true);
        $this->mailer->Subject = 'Thank you for getting in touch';
        $this->mailer->Body    = $this->confirmationTemplate($dto);
        $this->mailer->send();
    }

    public function sendNotification(ContactDTO $dto): void
    {
        $this->mailer->clearAddresses();
        $this->mailer->addAddress($_ENV['MAIL_TO']);
        $this->mailer->isHTML(true);
        $this->mailer->Subject = 'New contact form submission from ' . $dto->name;
        $this->mailer->Body    = $this->notificationTemplate($dto);
        $this->mailer->send();
    }

    private function confirmationTemplate(ContactDTO $dto): string
    {
        return "
            <p>Hi {$dto->name},</p>
            <p>Thank you for getting in touch. We have received your message and will get back to you shortly.</p>
            <p>Best regards</p>
        ";
    }

    private function notificationTemplate(ContactDTO $dto): string
    {
        return "
            <p><strong>Name:</strong> {$dto->name}</p>
            <p><strong>Email:</strong> {$dto->email}</p>
            <p><strong>Phone:</strong> {$dto->phone}</p>
            <p><strong>Message:</strong> {$dto->message}</p>
        ";
    }
}
