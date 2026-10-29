<?php

declare(strict_types=1);

namespace App\Domain\UserManagement\Model;

use App\Controller\Http\Api\Shared\Validation\ValidationFailedException;
use App\Core\Http\Exception\FieldValidationException;

class CreateUserModel
{
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
        $failure = new ValidationFailedException('Некорректные данные от пользователя.');

        $this->setFirstName($firstName, $failure);
        $this->setLastName($lastName, $failure);
        $this->setEmail($email, $failure);
        $this->setBirthDate($birthDate, $failure);

        if ($failure->hasErrors()) {
            throw $failure;
        }
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName, ValidationFailedException $failure): void
    {
        if (mb_strlen($firstName) < 2) {
            $failure->add(new FieldValidationException('firstName','Имя пользователя должно быть от 2 символов.'));
            return;
        }

        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName, ValidationFailedException $failure): void
    {
        if (mb_strlen($lastName) < 2) {
            $failure->add(new FieldValidationException('lastName', 'Фамилия пользователя должна быть от 2 символов.'));
            return;
        }

        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email, ValidationFailedException $failure): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $failure->add(new FieldValidationException('email', 'Пользователь указал невалидный email.'));
            return;
        }

        $this->email = $email;
    }

    public function getBirthDate(): \DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeImmutable $birthDate, ValidationFailedException $failure): void
    {
        if (new \DateTime('-18 years')->format('Y-m-d') < $birthDate->format('Y-m-d')) {
            $failure->add(new FieldValidationException('birthDate', 'Пользователь должен быть 18+.'));
            return;
        }

        $this->birthDate = $birthDate;
    }
}
