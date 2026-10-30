<?php

declare(strict_types=1);

use App\Controller\Http\Api\Shared\ErrorHandling\CompositeExceptionResponseResolver;
use App\Controller\Http\Api\Shared\ErrorHandling\DomainExceptionResponseResolver;
use App\Controller\Http\Api\Shared\ErrorHandling\ValidationExceptionResponseResolver;
use App\Controller\Http\Web\BankReport\Rule\ClientNameRule;
use App\Controller\Http\Web\BankReport\Rule\DateFormatRule;
use App\Controller\Http\Web\BankReport\Rule\DateRangeAndFutureRule;
use App\Controller\Http\Web\BankReport\Rule\EmailRule;
use App\Controller\Http\Web\BankReport\Rule\ReportTypeRule;
use App\Controller\Http\Web\BankReport\Validator\BankReportFormValidator;
use App\Core\Container\Config\Contracts\DotEnvConfigInterface;
use App\Core\Container\Container;
use App\Core\Database\Connection\DatabaseQueryExecutor;
use App\Core\Database\Connection\PDOWrapper;
use App\Core\Database\Factory\CollectionFactory;
use App\Core\Http\ErrorHandler\ExceptionResponseResolverInterface;
use App\Core\Storage\KeyValue\Memcached\MemcachedDriver;
use App\Core\Storage\KeyValue\Redis\RedisDriver;
use App\Domain\BankReport\Contract\ReportRequestPublisherInterface;
use App\Domain\BankReport\ReportService;
use App\Domain\BookshopSearch\BookshopService;
use App\Domain\BracketBalance\BracketBalancer;
use App\Domain\EmailVerification\EmailVerifier;
use App\Domain\EventSystem\EventService;
use App\Domain\EventSystem\Interface\EventRepositoryInterface;
use App\Domain\OrderNotification\Channel\Email\EmailMessageBuilder;
use App\Domain\OrderNotification\Channel\Email\EmailNotificationHandler;
use App\Domain\OrderNotification\Channel\NotificationHandlerFactory;
use App\Domain\OrderNotification\Channel\Sms\SmsMessageBuilder;
use App\Domain\OrderNotification\Channel\Sms\SmsNotificationHandler;
use App\Domain\OrderNotification\Contract\NotificationMailerInterface;
use App\Domain\OrderNotification\Contract\NotificationSmsSenderInterface;
use App\Domain\OrderNotification\Contract\NotificationTaskPublisherInterface;
use App\Domain\OrderNotification\Contract\NotificationTaskRepositoryInterface;
use App\Domain\OrderNotification\Contract\OrderDataProviderInterface;
use App\Domain\OrderNotification\NotificationTaskService;
use App\Domain\Shared\Validator\BracketBalanceValidator;
use App\Domain\Shared\Validator\DnsMxRecordValidator;
use App\Domain\Shared\Validator\EmailFormatValidator;
use App\Domain\Shared\Validator\EmailValidator;
use App\Domain\SystemHealth\SessionStorageChecker;
use App\Domain\SystemHealth\SystemHealthCheckService;
use App\Domain\UserManagement\UserService;
use App\Infrastructure\Database\DataMapper\NotificationTaskMapper;
use App\Infrastructure\Database\DataMapper\UserMapper;
use App\Infrastructure\Database\Repository\NotificationTaskRepository;
use App\Infrastructure\Database\Repository\UserRepository;
use App\Infrastructure\Http\Wiremock\OrderDataProvider;
use App\Infrastructure\Mail\Factory\ReportMailerFactory;
use App\Infrastructure\Mail\Mailer\NotificationMailer;
use App\Infrastructure\Mail\Mailpit\MailpitChaosClient;
use App\Infrastructure\RabbitMq\Config\NotificationAmqpConfig;
use App\Infrastructure\RabbitMq\Connection\AmqpConnection;
use App\Infrastructure\RabbitMq\Connection\AmqpConnectionFactory;
use App\Infrastructure\RabbitMq\Connection\AmqpConnectionInterface;
use App\Infrastructure\RabbitMq\Producer\RabbitMqProducer;
use App\Infrastructure\RabbitMq\Publisher\ReportRequestPublisher;
use App\Infrastructure\RabbitMq\Topology\BankReportTopology;
use App\Infrastructure\RabbitMq\Topology\OrderNotificationTopology;
use App\Infrastructure\Resolver\DnsResolver;
use App\Infrastructure\Resolver\DnsResolverInterface;
use App\Infrastructure\Sms\SmsMock\NotificationSmsSender;
use App\Infrastructure\Storage\KeyValue\Factory\EventRepositoryFactory;
use App\Infrastructure\Storage\Search\Elasticsearch\Client\SecureElasticsearchClientBuilder;
use App\Infrastructure\Storage\Search\Elasticsearch\Repository\BookshopRepository;
use Elastic\Elasticsearch\ClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;

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
        ExceptionResponseResolverInterface::class => [
            'singleton' => true,
            'factory' => static fn() => new CompositeExceptionResponseResolver([
                new ValidationExceptionResponseResolver(),
                new DomainExceptionResponseResolver(),
            ]),
        ],
        UserMapper::class => [
            'singleton' => true,
            'factory' => static fn() => new UserMapper(),
        ],
        UserRepository::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new UserRepository(
                $c->get(DatabaseQueryExecutor::class),
                $c->get(UserMapper::class),
                $c->get(CollectionFactory::class),
            )
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
    'BankReport' => [
        ClientNameRule::class => [
            'singleton' => true,
            'factory' => static fn() => new ClientNameRule(),
        ],
        DateFormatRule::class => [
            'singleton' => true,
            'factory' => static fn() => new DateFormatRule(),
        ],
        DateRangeAndFutureRule::class => [
            'singleton' => true,
            'factory' => static fn() => new DateRangeAndFutureRule(),
        ],
        ReportTypeRule::class => [
            'singleton' => true,
            'factory' => static fn() => new ReportTypeRule(),
        ],
        EmailRule::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new EmailRule($c->get(EmailFormatValidator::class)),
        ],
        BankReportFormValidator::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new BankReportFormValidator(
                $c->get(ClientNameRule::class),
                $c->get(DateFormatRule::class),
                $c->get(DateRangeAndFutureRule::class),
                $c->get(ReportTypeRule::class),
                $c->get(EmailRule::class),
            ),
        ],
        ReportService::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new ReportService(
                $c->get(ReportRequestPublisherInterface::class),
            ),
        ],
        AmqpConnectionInterface::class => [
            'singleton' => true,
            'factory' => static function(Container $c) {
                $factory = new AmqpConnectionFactory($c->get(DotEnvConfigInterface::class));
                return new AmqpConnection($factory->create());
            },
        ],
        BankReportTopology::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new BankReportTopology(
                $c->get(DotEnvConfigInterface::class),
            ),
        ],
        RabbitMqProducer::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new RabbitMqProducer(
                $c->get(AmqpConnectionInterface::class)
            ),
        ],
        ReportRequestPublisherInterface::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new ReportRequestPublisher(
                $c->get(RabbitMqProducer::class),
                $c->get(DotEnvConfigInterface::class),
            ),
        ],
        MailpitChaosClient::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new MailpitChaosClient(
                client: HttpClient::create(),
                baseUrl: $c->get(DotEnvConfigInterface::class)->get('MAILER_BASE_API_URL'),
            )
        ],
        ReportMailerFactory::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new ReportMailerFactory(
                $c->get(DotEnvConfigInterface::class),
                $c->get(MailpitChaosClient::class),
            ),
        ],
    ],
    'OrderNotification' => [
        NotificationTaskService::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new NotificationTaskService(
                $c->get(NotificationTaskRepositoryInterface::class),
                $c->get(NotificationTaskPublisherInterface::class),
                $c->get(OrderDataProviderInterface::class),
                $c->get(NotificationHandlerFactory::class),
            ),
        ],
        EmailMessageBuilder::class => [
            'singleton' => true,
            'factory' => static fn() => new EmailMessageBuilder(),
        ],
        EmailNotificationHandler::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new EmailNotificationHandler(
                $c->get(EmailMessageBuilder::class),
                $c->get(NotificationMailerInterface::class),
            ),
        ],
        SmsMessageBuilder::class => [
            'singleton' => true,
            'factory' => static fn() => new SmsMessageBuilder(),
        ],
        SmsNotificationHandler::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new SmsNotificationHandler(
                $c->get(SmsMessageBuilder::class),
                $c->get(NotificationSmsSenderInterface::class),
            ),
        ],
        NotificationHandlerFactory::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new NotificationHandlerFactory([
                $c->get(EmailNotificationHandler::class),
                $c->get(SmsNotificationHandler::class),
            ]),
        ],
        NotificationTaskRepositoryInterface::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new NotificationTaskRepository(
                $c->get(DatabaseQueryExecutor::class),
                $c->get(NotificationTaskMapper::class),
            ),
        ],
        NotificationTaskMapper::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new NotificationTaskMapper(),
        ],
        OrderDataProviderInterface::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new OrderDataProvider(
                client: HttpClient::create(),
                baseUrl: $c->get(DotEnvConfigInterface::class)->get('WIREMOCK_BASE_API_URL'),
            ),
        ],
        NotificationMailerInterface::class => [
            'singleton' => true,
            'factory' => static function(Container $c) {
                $config = $c->get(DotEnvConfigInterface::class);
                $dsn = Dsn::fromString($config->get('MAILER_DSN'));
                $transport = new EsmtpTransportFactory()->create($dsn);
                return new NotificationMailer(
                    mailer: new Mailer($transport),
                    fromEmail: $config->get('ORDER_NOTIFICATIONS_MAILER_FROM'),
                );
            },
        ],
        NotificationSmsSenderInterface::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new NotificationSmsSender(
                client: HttpClient::create(),
                baseUrl: $c->get(DotEnvConfigInterface::class)->get('SMS_BASE_API_URL'),
            ),
        ],
        NotificationAmqpConfig::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new NotificationAmqpConfig(
                $c->get(DotEnvConfigInterface::class),
            ),
        ],
        OrderNotificationTopology::class => [
            'singleton' => true,
            'factory' => static fn(Container $c) => new OrderNotificationTopology(
                $c->get(NotificationAmqpConfig::class),
            ),
        ],
    ],
];
