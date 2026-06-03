<?php

use App\Core\Container\Config\Data\DotEnv\DotEnvConfigInterface;
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
use App\Domain\UserManagement\UserService;
use App\Infrastructure\Database\Connection\DatabaseQueryExecutor;
use App\Infrastructure\Database\Connection\PDOWrapper;
use App\Infrastructure\Database\DataMapper\DataMapperInterface;
use App\Infrastructure\Database\DataMapper\UserMapper;
use App\Infrastructure\Database\Factory\CollectionFactory;
use App\Infrastructure\Database\Repository\UserRepository;
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
            'factory' => fn (Container $c) => new PDOWrapper($c->get(DotEnvConfigInterface::class)),
        ],
        RedisDriver::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new RedisDriver($c->get(DotEnvConfigInterface::class)),
        ],
        MemcachedDriver::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new MemcachedDriver($c->get(DotEnvConfigInterface::class)),
        ],
        SessionStorageChecker::class => [
            'singleton' => false,
            'factory' => fn (Container $c) => new SessionStorageChecker($c->get(RedisDriver::class)),
        ],
    ],
    'EventSystem' => [
        EventService::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new EventService($c->get(EventRepositoryInterface::class)),
        ],
        EventRepositoryFactory::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new EventRepositoryFactory(
                $c->get(DotEnvConfigInterface::class),
                $c->get(RedisDriver::class),
                $c->get(MemcachedDriver::class),
            ),
        ],
        EventRepositoryInterface::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => $c->get(EventRepositoryFactory::class)->create(),
        ],
    ],
    'UserManagement' => [
        DatabaseQueryExecutor::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new DatabaseQueryExecutor($c->get(PDOWrapper::class)),
        ],
        UserService::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new UserService($c->get(UserRepository::class)),
        ],
        DataMapperInterface::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new UserMapper(),
        ],
        CollectionFactory::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new CollectionFactory(),
        ],
        UserRepository::class => [
            'singleton' => true,
            'factory' => fn (Container $c) => new UserRepository(
                $c->get(DatabaseQueryExecutor::class),
                $c->get(DataMapperInterface::class),
                $c->get(CollectionFactory::class),
            ),
        ],
    ],
];