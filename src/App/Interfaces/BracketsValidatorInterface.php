<?php
declare(strict_types=1);

namespace App\Interfaces;

interface BracketsValidatorInterface
{
    /**
     * @throws \App\Exceptions\EmptyStringException
     * @throws \App\Exceptions\InvalidBracketsException
     */
    public function validate(string $input): bool;
}