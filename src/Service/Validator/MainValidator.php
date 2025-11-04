<?php

namespace Blarkinov\PhpDbCourse\Service\Validator;

use Exception;

class MainValidator implements ValidatorInterface
{
    public function validate(mixed $data = null): void
    {

        if (!isset($_SERVER['REQUEST_URI']))
            throw new Exception('Bad request');

        if (empty($_SERVER['REQUEST_URI']))
            throw new Exception('Bad request');

        if (!isset($_SERVER['REQUEST_METHOD']))
            throw new Exception('Bad request');

        if (empty($_SERVER['REQUEST_METHOD']))
            throw new Exception('Bad request');
    }

    protected function checkPostParam(string $name, string $type)
    {
        if (!isset($_POST[$name]))
            throw new Exception("not found param $name");
        if (empty($_POST[$name]))
            throw new Exception("empty param $name");

        if (gettype($_POST[$name]) !== $type)
            throw new Exception("wrong type $name");
    }

    protected function checkParam(mixed $param, string $type, string $name)
    {
        if (!isset($param))
            throw new Exception("not found param $name");
        if (empty($param))
            throw new Exception("empty param $name");

        if (gettype($param) !== $type)
            throw new Exception("wrong type $name");
    }

    protected function checkNumber($param, string $name)
    {
        if (empty($param) && $param !== 0)
            throw new Exception("empty param $name");

        if (!preg_match('/^-?\d+$/', $param))
            throw new Exception("wrong type $name");
    }

    protected function checkDate($param, string $name)
    {
        if (!isset($param))
            throw new Exception("not found param $name");
        if (empty($param))
            throw new Exception("empty param $name");

        if (!preg_match('/^(19|20)\d{2}\-(0[1-9]|1[0-2])\-(0[1-9]|[12]\d|3[01])$/', $param))
            throw new Exception("wrong type $name");
    }

    protected function checkGetParam(string $name, int $default)
    {
        if (!isset($_GET[$name]))
            $_GET[$name] = $default;
        if (!preg_match('/^-?\d+$/', ($_GET[$name])))
            throw new Exception("wrong type $name");
    }
}
