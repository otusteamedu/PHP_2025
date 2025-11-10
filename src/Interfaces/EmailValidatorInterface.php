<?php
declare(strict_types=1);

namespace App\Interfaces;

interface EmailValidatorInterface
{
    public function validate(array $emails): array;
}