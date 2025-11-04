<?php

namespace Blarkinov\PhpDbCourse\Service;

use Blarkinov\PhpDbCourse\Service\Validator\MainValidator;

class UserStoreValidator extends MainValidator
{

    public function validate(mixed $data = null): void
    {
        $this->checkPostParam('users', 'array');

        foreach ($_POST['users'] as  $user) {
            $this->checkParam($user['first_name'], 'string', 'first_name');
            $this->checkParam($user['last_name'], 'string', 'last_name');
            $this->checkDate($user['date_birth'], 'date_birth');
            $this->checkNumber($user['gender'], 'gender');
        }
    }
}
