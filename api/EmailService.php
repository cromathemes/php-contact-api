<?php

namespace Api;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private PHPMailer $mailer;
    private string $templatePath;

    public function __construct()
    {
        $this->templatePath = __DIR__ . '/Templates/';

        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host       = $_ENV['MAIL_HOST'];
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $_ENV['MAIL_USER'];
        $this->mailer->Password   = $_ENV['MAIL_PASS'];
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port       = (int) $_ENV['MAIL_PORT'];
        $this->mailer->setFrom($_ENV['MAIL_FROM'], 'Cro.ma');
    }

    public function sendConfirmation(ContactDTO $dto): void
    {
        $this->mailer->clearAddresses();
        $this->mailer->addAddress($dto->email, $dto->name);
        $this->mailer->isHTML(true);
        $this->mailer->Subject = 'Thank you for reaching out';
        $this->mailer->Body    = $this->render('confirmation.html', $dto);
        $this->mailer->send();
    }

    public function sendNotification(ContactDTO $dto): void
    {
        $this->mailer->clearAddresses();
        $this->mailer->addAddress($_ENV['MAIL_TO']);
        $this->mailer->isHTML(true);
        $this->mailer->Subject = 'New contact form submission from ' . $dto->name;
        $this->mailer->Body    = $this->render('notification.html', $dto);
        $this->mailer->send();
    }

    private function render(string $template, ContactDTO $dto): string
    {
        $path = $this->templatePath . $template;

        if (!file_exists($path)) {
            throw new \RuntimeException("Email template not found: {$template}");
        }

        $html = file_get_contents($path);

        return str_replace(
            ['{{NAME}}', '{{EMAIL}}', '{{PHONE}}', '{{MESSAGE}}'],
            [
                htmlspecialchars($dto->name,    ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($dto->email,   ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($dto->phone,   ENT_QUOTES, 'UTF-8'),
                nl2br(htmlspecialchars($dto->message, ENT_QUOTES, 'UTF-8')),
            ],
            $html
        );
    }
}