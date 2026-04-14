<?php

namespace App\Domain\Model;

use App\Domain\Validator\DnsMxRecordValidator;
use App\Domain\Validator\EmailFormatValidator;
use App\Domain\Validator\EmailValidator;

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
        $isValid = new EmailValidator(
            new EmailFormatValidator(),
            new DnsMxRecordValidator()
        )->isValid($newEmail);

        if (!$isValid) {
            throw new \Exception('Пользователь указал невалидный email.');
        }

        $this->newEmail = $newEmail;
    }
}
