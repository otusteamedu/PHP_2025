<?php

declare(strict_types=1);

namespace Otus\Contracts;

interface MxServiceInterface
{
    /**
     * @param string $email
     *
     * @return bool
     */
    public function validate(string $email): bool;
}
