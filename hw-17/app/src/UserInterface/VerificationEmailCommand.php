<?php

declare(strict_types=1);

namespace App\UserInterface;

use App\Application\VerificationEmailService;

readonly class VerificationEmailCommand
{
    public function __construct(
        private VerificationEmailService $verificationEmailService
    ) {
    }

    public function handle(): void
    {
        $this->verificationEmailService->handle();
    }
}
