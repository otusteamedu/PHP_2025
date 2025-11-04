<?php

namespace Blarkinov\PhpDbCourse\Service;

use Blarkinov\PhpDbCourse\Service\Validator\MainValidator;

class UserIndexValidator extends MainValidator
{
    public function validate(mixed $data = null): void
    {
        $this->checkGetParam('offset', 0);
        $this->checkGetParam('limit', 1000);
    }
}
