<?php

declare(strict_types=1);

namespace App\RequestService;

use Throwable;

class SomeProviderNameRequestService implements EmailValidatorRequestServiceInterface
{
    public function isEmailConfirmationSent(string $email): bool
    {
        try {
            //some send request and handle response logics
        } catch (Throwable) {
            return false;
        }

        return true;
    }
}
