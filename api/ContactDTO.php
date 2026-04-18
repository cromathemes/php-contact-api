<?php

namespace Api;

class ContactDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $message,
        public readonly string $nonce,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:    trim($data['name'] ?? ''),
            email:   trim($data['email'] ?? ''),
            phone:   trim($data['phone'] ?? ''),
            message: trim($data['message'] ?? ''),
            nonce:   trim($data['nonce'] ?? ''),
        );
    }
}