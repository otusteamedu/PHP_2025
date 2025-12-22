<?php

namespace Blarkinov\RabbitMq\Service;

use Blarkinov\RabbitMq\Exceptions\BadRequestException;
use Exception;

class Validator
{
    public function mainValidate(): void
    {
        if (!isset($_SERVER['REQUEST_URI']))
            throw new BadRequestException;

        if (empty($_SERVER['REQUEST_URI']))
            throw new BadRequestException;

        if (!isset($_SERVER['REQUEST_METHOD']))
            throw new BadRequestException;

        if (empty($_SERVER['REQUEST_METHOD']))
            throw new BadRequestException;
    }

    public function bankStatementPush()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            throw new BadRequestException;

        $this->checkParam('dateFrom', 'string');
        $this->checkParam('dateTo', 'string');
        $this->checkParam('transactionType', 'string');
    }

    public function bankStatementGet()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET')
            throw new BadRequestException;
    }

    private function checkParam(string $name, string $type)
    {
        if (!isset($_POST[$name]))
            throw new Exception("not found param $name");
        if (empty($_POST[$name]))
            throw new Exception("empty param $name");

        if (gettype($_POST[$name]) !== $type)
            throw new Exception("wrong type $name");
    }
}
