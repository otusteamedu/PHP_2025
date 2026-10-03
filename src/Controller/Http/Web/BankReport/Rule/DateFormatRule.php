<?php

declare(strict_types=1);

namespace App\Controller\Http\Web\BankReport\Rule;

class DateFormatRule implements RuleInterface
{
    private const string FORMAT = 'Y-m-d';

    public function validate(array $data, array &$errors): void
    {
        $this->checkDateFrom($data, $errors);
        $this->checkDateTo($data, $errors);
    }

    private function checkDateFrom(array $data, array &$errors): void
    {
        $dateFrom = $data['date_from'] ?? '';
        if ($dateFrom === '') {
            $errors['date_from'][] = 'Дата начала периода обязательна для заполнения.';
            return;
        }

        $date = \DateTime::createFromFormat(self::FORMAT, $dateFrom);
        if (!$date || $date->format(self::FORMAT) !== $dateFrom) {
            $errors['date_from'][] = 'Некорректный формат даты. Используйте формат YYYY-MM-DD.';
        }
    }

    private function checkDateTo(array $data, array &$errors): void
    {
        $dateTo = $data['date_to'] ?? '';
        if ($dateTo === '') {
            $errors['date_to'][] = 'Дата окончания периода обязательна для заполнения.';
            return;
        }

        $date = \DateTime::createFromFormat(self::FORMAT, $dateTo);
        if (!$date || $date->format(self::FORMAT) !== $dateTo) {
            $errors['date_to'][] = 'Некорректный формат даты. Используйте формат YYYY-MM-DD.';
        }
    }
}
