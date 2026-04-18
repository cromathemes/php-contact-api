<?php

namespace Api;

class ContactValidator
{
    private array $errors = [];

    public function validate(ContactDTO $dto): bool
    {
        $this->errors = [];

        if (empty($dto->name)) {
            $this->errors['name'] = 'Name is required.';
        }

        if (empty($dto->email)) {
            $this->errors['email'] = 'Email is required.';
        } elseif (!filter_var($dto->email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Email is invalid.';
        }

        if (empty($dto->phone)) {
            $this->errors['phone'] = 'Phone is required.';
        } elseif (!preg_match('/^\+?[\d\s\-\(\)]{7,20}$/', $dto->phone)) {
            $this->errors['phone'] = 'Phone number is invalid.';
        }

        if (empty($dto->message)) {
            $this->errors['message'] = 'Message is required.';
        } elseif (strlen($dto->message) < 10) {
            $this->errors['message'] = 'Message must be at least 10 characters.';
        }

        if (empty($dto->nonce)) {
            $this->errors['nonce'] = 'Invalid request.';
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
