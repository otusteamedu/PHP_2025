<?php

declare(strict_types=1);

use App\Core\Container\Config\Contracts\DotEnvConfigInterface;
use App\Core\Container\Container;
use App\Core\Database\Connection\DatabaseQueryExecutor;
use App\Core\Database\Connection\PDOWrapper;
use App\Core\Database\Factory\CollectionFactory;
use App\Core\Storage\KeyValue\Memcached\MemcachedDriver;
use App\Core\Storage\KeyValue\Redis\RedisDriver;
use App\Domain\BookshopSearch\BookshopService;
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
use App\Domain\UserManagement\Factory\UserRepositoryFactory;
use App\Domain\UserManagement\UserService;
use App\Infrastructure\Database\DataMapper\UserMapper;
use App\Infrastructure\Database\Repository\UserRepository;
use App\Infrastructure\Resolver\DnsResolver;
use App\Infrastructure\Resolver\DnsResolverInterface;
use App\Infrastructure\Storage\KeyValue\Factory\EventRepositoryFactory;
use App\Infrastructure\Storage\Search\Elasticsearch\Client\SecureElasticsearchClientBuilder;
use App\Infrastructure\Storage\Search\Elasticsearch\Repository\BookshopRepository;
use Elastic\Elasticsearch\ClientInterface;

return [
    'EmailVerification' => [
        DnsResolverInterface::class => [
            'singleton' => true,
            'factory' => static fn() => new DnsResolver(),
        ],
        EmailFormatValidator::class => [
            'singleton' => true,
            'factory' => static fn() => new EmailFormatValidator(),
        ],
        DnsMxRecordValidator::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new DnsMxRecordValidator(
                $c->get(DnsResolverInterface::class),
            ),
        ],
        EmailValidator::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new EmailValidator(
                $c->get(EmailFormatValidator::class),
                $c->get(DnsMxRecordValidator::class),
            ),
        ],
        EmailVerifier::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new EmailVerifier($c->get(EmailValidator::class)),
        ],
    ],
    'BracketBalance' => [
        BracketBalanceValidator::class => [
            'singleton' => true,
            'factory' => static fn() => new BracketBalanceValidator(),
        ],
        BracketBalancer::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new BracketBalancer($c->get(BracketBalanceValidator::class)),
        ],
    ],
    'SystemHealth' => [
        SessionStorageChecker::class => [
            'singleton' => false,
            'factory' => static fn(Container $c) => new SessionStorageChecker($c->get(RedisDriver::class)),
        ],
        SystemHealthCheckService::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new SystemHealthCheckService(
                $c->get(PDOWrapper::class),
                $c->get(RedisDriver::class),
                $c->get(MemcachedDriver::class),
                $c->get(SessionStorageChecker::class),
            ),
        ],
    ],
    'EventSystem' => [
        EventRepositoryFactory::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new EventRepositoryFactory(
                $c->get(DotEnvConfigInterface::class),
                $c->get(RedisDriver::class),
                $c->get(MemcachedDriver::class),
            ),
        ],
        EventRepositoryInterface::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => $c->get(EventRepositoryFactory::class)->create(),
        ],
        EventService::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new EventService($c->get(EventRepositoryInterface::class)),
        ],
    ],
    'UserManagement' => [
        UserMapper::class => [
            'singleton' => true,
            'factory' => static fn() => new UserMapper(),
        ],
        UserRepositoryFactory::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new UserRepositoryFactory(
                $c->get(DatabaseQueryExecutor::class),
                $c->get(CollectionFactory::class),
            ),
        ],
        UserRepository::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => $c->get(UserRepositoryFactory::class)
                ->create($c->get(UserMapper::class)),
        ],
        UserService::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new UserService($c->get(UserRepository::class)),
        ],
    ],
    'BookshopSearch' => [
        ClientInterface::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new SecureElasticsearchClientBuilder(
                $c->get(DotEnvConfigInterface::class),
            )->build(),
        ],
        BookshopRepository::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new BookshopRepository($c->get(ClientInterface::class)),
        ],
        BookshopService::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new BookshopService($c->get(BookshopRepository::class)),
        ],
    ],
];
