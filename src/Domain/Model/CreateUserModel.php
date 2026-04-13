<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Validator\DnsMxRecordValidator;
use App\Domain\Validator\EmailFormatValidator;
use App\Domain\Validator\EmailValidator;
use Customer41\MultiException\MultiException;

class CreateUserModel
{
    private readonly MultiException $validationErrors;

    private string $firstName;
    private string $lastName;
    private string $email;
    private \DateTimeImmutable $birthDate;

    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        \DateTimeImmutable $birthDate,
    ) {
        $this->validationErrors = new MultiException('Некорректные данные от пользователя.', 400);

        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setEmail($email);
        $this->setBirthDate($birthDate);

        if ($this->validationErrors->count() !== 0) {
            throw $this->validationErrors;
        }
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        if (mb_strlen($firstName) < 2) {
            $this->validationErrors->add(new \Exception('Имя пользователя должно быть от 2 символов.'));
            return;
        }

        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        if (mb_strlen($lastName) < 2) {
            $this->validationErrors->add(new \Exception('Фамилия пользователя должна быть от 2 символов.'));
            return;
        }

        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $isValid = new EmailValidator(
            new EmailFormatValidator(),
            new DnsMxRecordValidator(),
        )->isValid($email);

        if (!$isValid) {
            $this->validationErrors->add(new \Exception('Пользователь указал невалидный email.'));
            return;
        }

        $this->email = $email;
    }

    public function getBirthDate(): \DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeImmutable $birthDate): void
    {
        if (new \DateTime('-18 years')->format('Y-m-d') < $birthDate->format('Y-m-d')) {
            $this->validationErrors->add(new \Exception('Пользователь должен быть 18+.'));
            return;
        }

        $this->birthDate = $birthDate;
    }
}
