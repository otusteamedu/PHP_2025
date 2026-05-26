<?php

use App\Core\Config\ConfigInterface;
use App\Core\Container\Container;
use App\Domain\BracketBalance\BracketBalancer;
use App\Domain\EmailVerification\EmailVerifier;
use App\Domain\EventSystem\EventService;
use App\Domain\EventSystem\Interface\EventRepositoryInterface;
use App\Domain\Shared\Validator\BracketBalanceValidator;
use App\Domain\Shared\Validator\DnsMxRecordValidator;
use App\Domain\Shared\Validator\EmailFormatValidator;
use App\Domain\Shared\Validator\EmailValidator;
use App\Domain\SystemHealth\SessionStorageChecker;
use App\Domain\SystemHealth\SystemHealthCheckService;
use App\Infrastructure\Database\Connection\PDOWrapper;
use App\Infrastructure\Storage\KeyValue\Driver\MemcachedDriver;
use App\Infrastructure\Storage\KeyValue\Driver\RedisDriver;
use App\Infrastructure\Storage\KeyValue\Factory\EventRepositoryFactory;

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
    'BracketBalance' => [
        BracketBalancer::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new BracketBalancer($c->get(BracketBalanceValidator::class)),
        ],
        BracketBalanceValidator::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new BracketBalanceValidator(),
        ],
    ],
    'SystemHealth' => [
        SystemHealthCheckService::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new SystemHealthCheckService(
                $c->get(PDOWrapper::class),
                $c->get(RedisDriver::class),
                $c->get(MemcachedDriver::class),
                $c->get(SessionStorageChecker::class),
            ),
        ],
        PDOWrapper::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new PDOWrapper($c->get(ConfigInterface::class)),
        ],
        RedisDriver::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new RedisDriver($c->get(ConfigInterface::class)),
        ],
        MemcachedDriver::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new MemcachedDriver($c->get(ConfigInterface::class)),
        ],
        SessionStorageChecker::class => [
            'singleton' => false,
            'factory' => fn (Container $c) => new SessionStorageChecker($c->get(RedisDriver::class)),
        ],
    ],
    'EventService' => [
        EventService::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new EventService($c->get(EventRepositoryInterface::class)),
        ],
        EventRepositoryFactory::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new EventRepositoryFactory(
                $c->get(ConfigInterface::class),
                $c->get(RedisDriver::class),
                $c->get(MemcachedDriver::class),
            ),
        ],
        EventRepositoryInterface::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => $c->get(EventRepositoryFactory::class)->create(),
        ],
    ],
];