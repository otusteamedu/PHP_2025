<?php

namespace Blarkinov\PhpDbCourse\Service\Validator;

interface ValidatorInterface
{
    public function validate(mixed $data = null): void;
}
