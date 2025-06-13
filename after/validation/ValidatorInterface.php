<?php

namespace App\validation;

interface ValidatorInterface {
    public function isValid(string $string): bool;
}
