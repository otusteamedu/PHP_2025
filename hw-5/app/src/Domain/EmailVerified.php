<?php

declare(strict_types=1);

namespace App\Domain;

interface EmailVerified
{
    public function verify(Email $email): bool;
}
