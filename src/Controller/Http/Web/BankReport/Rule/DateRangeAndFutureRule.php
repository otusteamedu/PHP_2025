<?php

declare(strict_types=1);

namespace App\Controller\Http\Web\BankReport\Rule;

class DateRangeAndFutureRule implements RuleInterface
{
    private const string FORMAT = 'Y-m-d';

    public function validate(array $data, array &$errors): void
    {
        if (isset($errors['date_from']) || isset($errors['date_to'])) {
            return;
        }

        $from = \DateTime::createFromFormat(self::FORMAT, $data['date_from']);
        $to = \DateTime::createFromFormat(self::FORMAT, $data['date_to']);
        $now = new \DateTime();

        if ($to < $from) {
            $errors['date_to'][] = 'Дата "до" не может быть раньше даты "от".';
        }

        if ($from > $now) {
            $errors['date_from'][] = 'Дата начала периода не может быть позже текущей даты.';
        }

        if ($to > $now) {
            $errors['date_to'][] = 'Дата окончания периода не может быть позже текущей даты.';
        }
    }
}
