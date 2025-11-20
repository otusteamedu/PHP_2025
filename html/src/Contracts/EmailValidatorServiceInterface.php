<?php

declare(strict_types=1);

namespace Otus\Contracts;

interface EmailValidatorServiceInterface
{
    /**
     * @param string $email
     *
     * @return bool
     */
    public function handle(string $email): bool;
}
