<?php

namespace App\Interface;

interface RequestValidatorInterface {
    public function validateRequest(): array;
}