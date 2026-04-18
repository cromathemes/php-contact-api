<?php

namespace Api;

class TokenService
{
    private int $expiry;

    public function __construct()
    {
        $this->expiry = (int) $_ENV['TOKEN_EXPIRY'] ?? 900;
    }

    public function generate(): string
    {
        $this->startSession();

        $nonce = bin2hex(random_bytes(32));

        $_SESSION['nonce']        = $nonce;
        $_SESSION['nonce_expiry'] = time() + $this->expiry;

        return $nonce;
    }

    public function verify(string $nonce): bool
    {
        $this->startSession();

        if (empty($_SESSION['nonce']) || empty($_SESSION['nonce_expiry'])) {
            return false;
        }

        if (time() > $_SESSION['nonce_expiry']) {
            $this->burn();
            return false;
        }

        if (!hash_equals($_SESSION['nonce'], $nonce)) {
            return false;
        }

        $this->burn();
        return true;
    }

    private function burn(): void
    {
        unset($_SESSION['nonce']);
        unset($_SESSION['nonce_expiry']);
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'secure'   => true,
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
            session_start();
        }
    }
}
