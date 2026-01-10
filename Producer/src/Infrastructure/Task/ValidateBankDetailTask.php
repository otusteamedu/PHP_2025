<?php

namespace Producer\Infrastructure\Task;


use Producer\Application\Task\Task;
use Producer\Infrastructure\Exception\ValidationException;

class ValidateBankDetailTask extends Task
{
    /**
     * @throws ValidationException
     */
    public function run(array $data): void {
        $account = $data['account'] ?? null;
        if (!is_string($account) || strlen($account) != 20 || empty($account)) {
            throw new ValidationException('Строка account должна быть 20 символам');
        }

        $client = $data['client'] ?? null;
        if (!is_string($client) || empty($client)) {
            throw new ValidationException('Строка client должна быть ФИО');
        }

        $bank = $data['bank'] ?? null;
        if (!is_string($bank) || empty($bank)) {
            throw new ValidationException('Строка bank должна быть Наименованием банка');
        }

        $bik = $data['bik'] ?? null;
        if (!is_string($bik) || strlen($bik) != 9 || empty($bik)) {
            throw new ValidationException('Строка bik должна быть 9 символам');
        }
    }
}