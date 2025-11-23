<?php

declare(strict_types=1);

namespace App\Domain;

interface EmailVerifiedInterface
{
    public function verify(Email $email): bool;
}
