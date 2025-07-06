<?php

namespace App\Exception;

class NoMxRecordException extends \InvalidArgumentException implements IApplicationException
{
    public function __construct(string $domain) {
        parent::__construct("Не найден dns сервер для домена $domain");
    }
    public function getHttpCode(): int
    {
        return 400;
    }
}