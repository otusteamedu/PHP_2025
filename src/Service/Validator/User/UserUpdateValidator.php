<?php

namespace Blarkinov\PhpDbCourse\Service;

use Blarkinov\PhpDbCourse\Service\Validator\MainValidator;

class UserUpdateValidator extends MainValidator
{

    public function validate(mixed $data = null): void
    {
        $this->checkNumber($data, 'id');
        if (isset($_POST['first_name'])) $this->checkPostParam('first_name', 'string');
        if (isset($_POST['last_name'])) $this->checkPostParam('last_name', 'string');
        if (isset($_POST['date_birth'])) $this->checkDate($_POST['date_birth'], 'date_birth');
        if (isset($_POST['gender'])) $this->checkNumber($_POST['gender'], 'gender');
    }
}
