<?php

namespace Blarkinov\RedisCourse\Service;

use Exception;

class Validator
{
    public function mainValidate(): bool
    {

        if (!isset($_SERVER['REQUEST_URI']))
            return false;

        if (empty($_SERVER['REQUEST_URI']))
            return false;

        if (!isset($_SERVER['REQUEST_METHOD']))
            return false;

        if (empty($_SERVER['REQUEST_METHOD']))
            return false;

        return true;
    }

    public function eventSave()
    {
        $this->checkParam('priority', 'integer');
        $this->checkParam('data', 'array');
        $this->checkParam('conditions', 'array');
    }

    public function eventPriority()
    {
        $this->checkParam('conditions', 'array');
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
