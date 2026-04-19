<?php

namespace Api;

class ContactController
{
    private ContactValidator $validator;
    private TokenService     $tokenService;
    private EmailService     $emailService;

    public function __construct()
    {
        $this->validator    = new ContactValidator();
        $this->tokenService = new TokenService();
        $this->emailService = new EmailService();
    }

    public function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        match($method) {
            'GET'     => $this->handleNonce(),
            'POST'    => $this->handleSubmission(),
            'OPTIONS' => $this->handleOptions(),
            default   => $this->respond(405, ['error' => 'Method not allowed']),
        };
    }

    private function handleNonce(): void
    {
        $nonce = $this->tokenService->generate();
        $this->respond(200, ['nonce' => $nonce]);
    }

    private function handleSubmission(): void
    {
        $body = json_decode(file_get_contents('php://input'), true);

        if (!$body) {
            $this->respond(400, ['error' => 'Invalid request body']);
            return;
        }

        $dto = ContactDTO::fromArray($body);

        if (!$this->tokenService->verify($dto->nonce)) {
            $this->respond(403, ['error' => 'Invalid or expired token']);
            return;
        }

        if (!$this->validator->validate($dto)) {
            $this->respond(422, ['errors' => $this->validator->getErrors()]);
            return;
        }

        try {
            $this->emailService->sendNotification($dto);
            $this->emailService->sendConfirmation($dto);
            $this->respond(200, ['message' => 'Your message has been sent successfully']);
        } catch (\Exception $e) {
            $this->respond(500, ['error' => 'Failed to send email. Please try again later.']);
        }
    }

    private function handleOptions(): void
    {
        $this->respond(200, []);
    }

    private function respond(int $status, array $data): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
