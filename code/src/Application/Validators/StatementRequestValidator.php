<?php

declare(strict_types=1);

namespace Queues\Application\Validators;

use Queues\Application\DTO\StatementRequestDTO;
use Queues\Application\Interfaces\StatementRequestValidatorInterface;

class StatementRequestValidator implements StatementRequestValidatorInterface
{
    public function validate(StatementRequestDTO $dto): void
    {
        $fromTs = $this->validateDate($dto->dateFrom, 'Дата от');
        $toTs = $this->validateDate($dto->dateTo, 'Дата до');
        $this->validateEmail($dto->email);

        if ($fromTs > $toTs) {
            throw new \InvalidArgumentException('Дата от не может быть больше даты до');
        }
    }

    private function validateDate(string $date, string $fieldName): int
    {
        if (empty($date)) {
            throw new \InvalidArgumentException("Поле \"$fieldName\" обязательно");
        }

        $timestamp = strtotime($date);
        if ($timestamp === false) {
            throw new \InvalidArgumentException("Поле \"$fieldName\" должно быть корректной датой");
        }

        return $timestamp;
    }

    private function validateEmail(string $email): void
    {
        if (empty($email)) {
            throw new \InvalidArgumentException('Поле "Email" обязательно');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Некорректный email');
        }
    }
}
