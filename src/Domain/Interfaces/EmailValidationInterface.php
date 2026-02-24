<?php

namespace EmailsVerifier\Domain\Interfaces;

use EmailsVerifier\Domain\Email;

interface EmailValidationInterface
{
    public function validate(Email $email): array;
}
