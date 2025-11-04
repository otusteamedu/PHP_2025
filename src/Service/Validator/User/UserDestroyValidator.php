<?php

namespace Blarkinov\PhpDbCourse\Service;

use Blarkinov\PhpDbCourse\Service\Validator\MainValidator;

class UserDestroyValidator extends MainValidator
{

    public function validate(mixed $data = null): void
    {
        $this->checkNumber($data, 'id');
    }
}
