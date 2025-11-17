<?php
declare(strict_types=1);

namespace App\Domain\Interfaces;

interface EmailValidatorInterface
{

    public function validate(array $arEmails): array;
}