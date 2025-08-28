<?php

namespace App\Interface;

interface EmailValidatorInterface {
    public function verifyEmails(array $emails): array;
}