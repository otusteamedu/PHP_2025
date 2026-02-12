<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use App\Domain\Email;

interface EmailVerifiedInterface
{
    public function verify(Email $email): bool;
}
