<?php

namespace Blarkinov\PhpDbCourse\Service;

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


    public function store()
    {
        $this->checkPostParam('users', 'array');


        foreach ($_POST['users'] as  $user) {
            $this->checkParam($user['first_name'], 'string', 'first_name');
            $this->checkParam($user['last_name'], 'string', 'last_name');
            $this->checkDate($user['date_birth'], 'date_birth');
            $this->checkNumber($user['gender'], 'gender');
        }
    }


    public function show($id)
    {
        $this->checkNumber($id, 'id');
    }

    public function update($id)
    {
        $this->checkNumber($id, 'id');
        if (isset($_POST['first_name'])) $this->checkPostParam('first_name', 'string');
        if (isset($_POST['last_name'])) $this->checkPostParam('last_name', 'string');
        if (isset($_POST['date_birth'])) $this->checkDate($_POST['date_birth'], 'date_birth');
        if (isset($_POST['gender'])) $this->checkNumber($_POST['gender'], 'gender');
    }

    public function destroy($id)
    {
        $this->checkNumber($id, 'id');
    }

    private function checkPostParam(string $name, string $type)
    {
        if (!isset($_POST[$name]))
            throw new Exception("not found param $name");
        if (empty($_POST[$name]))
            throw new Exception("empty param $name");

        if (gettype($_POST[$name]) !== $type)
            throw new Exception("wrong type $name");
    }

    private function checkParam(mixed $param, string $type, string $name)
    {
        if (!isset($param))
            throw new Exception("not found param $name");
        if (empty($param))
            throw new Exception("empty param $name");

        if (gettype($param) !== $type)
            throw new Exception("wrong type $name");
    }

    private function checkNumber($param, string $name)
    {
        if (empty($param) && $param !== 0)
            throw new Exception("empty param $name");

        if (!preg_match('/^-?\d+$/', $param))
            throw new Exception("wrong type $name");
    }

    private function checkDate($param, string $name)
    {
        if (!isset($param))
            throw new Exception("not found param $name");
        if (empty($param))
            throw new Exception("empty param $name");

        if (!preg_match('/^(19|20)\d{2}\-(0[1-9]|1[0-2])\-(0[1-9]|[12]\d|3[01])$/', $param))
            throw new Exception("wrong type $name");
    }
}
