<?php

declare(strict_types=1);

namespace Infrastructure\Services;

class SmsService {
    public function send(string $phone, string $message): bool
    {
        return true;
    }
}

