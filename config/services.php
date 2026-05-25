<?php

use App\Core\Container\Container;
use App\Domain\EmailVerification\EmailVerifier;
use App\Domain\Shared\Validator\DnsMxRecordValidator;
use App\Domain\Shared\Validator\EmailFormatValidator;
use App\Domain\Shared\Validator\EmailValidator;

return [
    'EmailVerification' => [
        EmailVerifier::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new EmailVerifier($c->get(EmailValidator::class)),
        ],
        EmailValidator::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new EmailValidator(
                $c->get(EmailFormatValidator::class),
                $c->get(DnsMxRecordValidator::class),
            ),
        ],
        EmailFormatValidator::class => [
            'singleton' => true,
            'factory' => fn() => new EmailFormatValidator(),
        ],
        DnsMxRecordValidator::class => [
            'singleton' => true,
            'factory' => fn() => new DnsMxRecordValidator(),
        ],
    ],
];