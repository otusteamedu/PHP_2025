<?php

declare(strict_types=1);

namespace App\Domain\UserManagement\Model;

class UpdateUserEmailModel
{
    private string $newEmail;

    public function __construct(
        private readonly int $userId,
        string $newEmail,
    ) {
        $this->setNewEmail($newEmail);
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getNewEmail(): string
    {
        return $this->newEmail;
    }

    public function setNewEmail(string $newEmail): void
    {
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Пользователь указал невалидный email.');
        }

        $this->newEmail = $newEmail;
    }
}
