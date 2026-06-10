<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use MaxMessenger\Bot\Contracts\MaxApiConfigInterface;
use MaxMessenger\Bot\MaxApiClient;
use MkdBot\Application\Service\MainMenuSender;
use MkdBot\Application\UseCase\DeliverNews;
use MkdBot\Application\UseCase\ForwardToTelegram;
use MkdBot\Application\UseCase\GetContacts;
use MkdBot\Application\UseCase\HandleBotStarted;
use MkdBot\Application\UseCase\HandleBotStopped;
use MkdBot\Application\UseCase\HandleChannelMessage;
use MkdBot\Application\UseCase\HandleDialogMessage;
use MkdBot\Application\UseCase\HandleMaxWebhook;
use MkdBot\Application\UseCase\HandleMessageCallback;
use MkdBot\Application\UseCase\HandleTelegramWebhook;
use MkdBot\Application\UseCase\ProcessProposal;
use MkdBot\Application\Service\RagResponseFormatter;
use MkdBot\Application\UseCase\ProcessRagQuery;
use MkdBot\Application\UseCase\SendNewsToUser;
use MkdBot\Domain\Interface\BotSubscriberRepositoryInterface;
use MkdBot\Domain\Interface\ContactRepositoryInterface;
use MkdBot\Domain\Interface\ConversationStateRepositoryInterface;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Domain\Interface\FallbackMessageRepositoryInterface;
use MkdBot\Domain\Interface\MaxBotClientInterface;
use MkdBot\Domain\Interface\MaxBotConfigInterface;
use MkdBot\Domain\Interface\NewsDeliveryRepositoryInterface;
use MkdBot\Domain\Interface\NewsRepositoryInterface;
use MkdBot\Domain\Interface\ProcessedWebhookRepositoryInterface;
use MkdBot\Domain\Interface\ProposalNotifierInterface;
use MkdBot\Domain\Interface\ProposalRepositoryInterface;
use MkdBot\Domain\Interface\QueuePublisherInterface;
use MkdBot\Domain\Interface\RabbitMQConnectionInterface;
use MkdBot\Domain\Interface\RagSearchClientInterface;
use MkdBot\Domain\Interface\TelegramBotClientInterface;
use MkdBot\Infrastructure\Interface\MaxWebhookRegistrarInterface;
use MkdBot\Infrastructure\Interface\TelegramWebhookClientInterface;
use MkdBot\Infrastructure\Logging\LoggerFactory;
use MkdBot\Infrastructure\MaxBot\MaxBotClient;
use MkdBot\Infrastructure\MaxBot\MaxBotConfig;
use MkdBot\Infrastructure\MaxBot\MaxWebhookRegistrar;
use MkdBot\Infrastructure\Notification\PhpMailerProposalNotifier;
use MkdBot\Infrastructure\Persistence\MigrationRunner;
use MkdBot\Infrastructure\Persistence\PostgresBotSubscriberRepository;
use MkdBot\Infrastructure\Persistence\PostgresConnection;
use MkdBot\Infrastructure\Persistence\PostgresContactRepository;
use MkdBot\Infrastructure\Persistence\PostgresConversationStateRepository;
use MkdBot\Infrastructure\Persistence\PostgresFallbackMessageRepository;
use MkdBot\Infrastructure\Persistence\PostgresNewsDeliveryRepository;
use MkdBot\Infrastructure\Persistence\PostgresNewsRepository;
use MkdBot\Infrastructure\Persistence\PostgresProcessedWebhookRepository;
use MkdBot\Infrastructure\Persistence\PostgresProposalRepository;
use MkdBot\Infrastructure\Queue\FallbackConsumer;
use MkdBot\Infrastructure\Queue\NewsDeliveryConsumer;
use MkdBot\Infrastructure\Queue\RabbitMQConnection;
use MkdBot\Infrastructure\Queue\RabbitMQConnectionFactory;
use MkdBot\Infrastructure\Queue\RabbitMQPublisher;
use MkdBot\Infrastructure\Queue\RagQueryConsumer;
use MkdBot\Infrastructure\Queue\TelegramForwardConsumer;
use MkdBot\Infrastructure\RagSearch\RagSearchClient;
use MkdBot\Infrastructure\TelegramBot\TelegramBotClient;
use MkdBot\Infrastructure\TelegramBot\TelegramLongPollWorker;
use MkdBot\Presentation\Controller\HealthController;
use MkdBot\Presentation\Controller\MaxWebhookController;
use MkdBot\Presentation\Controller\TelegramWebhookController;
use MkdBot\Presentation\Mapper\MaxUpdateMapper;
use MkdBot\Presentation\Middleware\MaxWebhookAuthMiddleware;
use MkdBot\Presentation\Middleware\TelegramWebhookAuthMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

return [
        // Настройки
        'settings' => fn(ContainerInterface $c) => require __DIR__ . '/settings.php',

        // Max Bot — строковые параметры из настроек
        'max.token' => fn(ContainerInterface $c) => $c->get('settings')['max']['token'],
        'max.channel' => fn(ContainerInterface $c) => $c->get('settings')['max']['channel'],
        'max.webhook_secret' => fn(ContainerInterface $c) => $c->get('settings')['max']['webhook_secret'],

        // Telegram secret token (опционально — null если не установлен)
        'telegram.secret_token' => fn(ContainerInterface $c) => $c->get('settings')['telegram']['secret_token'] ?? null,

        // Логирование — Monolog через LoggerFactory
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');
            $loggerSettings = $settings['logger'];
            $appSettings = $settings['app'];
    
            return $c->get(LoggerFactory::class)->create(
                $appSettings['name'],
                $loggerSettings['path'],
                $loggerSettings['level'],
            );
        },

        // БД — PostgresConnection (реализация DatabaseConnectionInterface)
        DatabaseConnectionInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');
            $pg = $settings['postgres'];

            return new PostgresConnection(
                host: $pg['host'],
                port: $pg['port'],
                database: $pg['database'],
                user: $pg['user'],
                password: $pg['password'],
            );
        },

        // PDO — через PostgresConnection (для обратной совместимости)
        PDO::class => function (ContainerInterface $c) {
            return $c->get(DatabaseConnectionInterface::class)->getConnection();
        },

        // Механизм миграций
        MigrationRunner::class => function (ContainerInterface $c) {
            return new MigrationRunner(
                db: $c->get(DatabaseConnectionInterface::class),
                logger: $c->get(LoggerInterface::class),
                migrationsDir: __DIR__ . '/migrations',
            );
        },

        // Репозитории
        ProposalRepositoryInterface::class => DI\create(PostgresProposalRepository::class)
            ->constructor(DI\get(DatabaseConnectionInterface::class)),

        ConversationStateRepositoryInterface::class => DI\create(PostgresConversationStateRepository::class)
            ->constructor(DI\get(DatabaseConnectionInterface::class)),

        ContactRepositoryInterface::class => DI\create(PostgresContactRepository::class)
            ->constructor(DI\get(DatabaseConnectionInterface::class)),

        ProcessedWebhookRepositoryInterface::class => DI\create(PostgresProcessedWebhookRepository::class)
            ->constructor(DI\get(DatabaseConnectionInterface::class)),

        FallbackMessageRepositoryInterface::class => DI\create(PostgresFallbackMessageRepository::class)
            ->constructor(DI\get(DatabaseConnectionInterface::class)),
    
        NewsRepositoryInterface::class => DI\create(PostgresNewsRepository::class)
            ->constructor(DI\get(DatabaseConnectionInterface::class)),
    
        NewsDeliveryRepositoryInterface::class => DI\create(PostgresNewsDeliveryRepository::class)
            ->constructor(DI\get(DatabaseConnectionInterface::class)),
    
        BotSubscriberRepositoryInterface::class => DI\create(PostgresBotSubscriberRepository::class)
            ->constructor(DI\get(DatabaseConnectionInterface::class)),
    
        // Max Bot — конфигурация
        MaxBotConfigInterface::class => DI\create(MaxBotConfig::class)
            ->constructor(DI\get('max.token'))
            ->method('setRetryAttempts', [1000, 2000, 4000]) // 3 попытки вместо 5 по умолчанию
            ->method('setTimeout', 10000) // 10 сек вместо 1 сек по умолчанию
            ->method('setConnectTimeout', 5000),
        
        // Для совместимости с MaxApiClient — внутренний MaxApiConfig
        MaxApiConfigInterface::class => DI\factory(fn(ContainerInterface $c) => $c->get(MaxBotConfigInterface::class)->getInnerConfig()),

        // Max Bot — клиент
        MaxApiClient::class => DI\create(MaxApiClient::class)
            ->constructor(DI\get(MaxApiConfigInterface::class)),

        MaxBotClientInterface::class => DI\create(MaxBotClient::class)
            ->constructor(DI\get(MaxApiClient::class), DI\get(LoggerInterface::class)),

        // Max Bot — регистратор webhook
        MaxWebhookRegistrarInterface::class => DI\create(MaxWebhookRegistrar::class)
            ->constructor(DI\get(MaxApiClient::class), DI\get(LoggerInterface::class)),

        // Telegram Bot — строковые параметры из настроек
        'telegram.token' => fn(ContainerInterface $c) => $c->get('settings')['telegram']['token'],
        'telegram.channel' => fn(ContainerInterface $c) => $c->get('settings')['telegram']['channel'],
        'telegram.http_proxy' => fn(ContainerInterface $c) => $c->get('settings')['telegram']['http_proxy'] ?? '',
        'telegram.proxy_login' => fn(ContainerInterface $c) => $c->get('settings')['telegram']['proxy_login'] ?? '',
        'telegram.proxy_password' => fn(ContainerInterface $c) => $c->get('settings')['telegram']['proxy_password'] ?? '',
        
        // Telegram Bot — клиент (реализует Domain + Infrastructure интерфейсы)
        // Передаём http_proxy и аутентификацию прокси для маршрутизации запросов к Telegram API
        TelegramBotClientInterface::class => DI\create(TelegramBotClient::class)
            ->constructor(
                DI\get('telegram.token'),
                DI\get(LoggerInterface::class),
                DI\get('telegram.http_proxy'),
                DI\get('telegram.proxy_login'),
                DI\get('telegram.proxy_password'),
            ),
        
        TelegramWebhookClientInterface::class => DI\get(TelegramBotClientInterface::class),
        
        // SMTP — строковые параметры из настроек
        'smtp.host' => fn(ContainerInterface $c) => $c->get('settings')['smtp']['host'],
        'smtp.port' => fn(ContainerInterface $c) => $c->get('settings')['smtp']['port'],
        'smtp.user' => fn(ContainerInterface $c) => $c->get('settings')['smtp']['user'],
        'smtp.password' => fn(ContainerInterface $c) => $c->get('settings')['smtp']['password'],
        'smtp.from_email' => fn(ContainerInterface $c) => $c->get('settings')['smtp']['from_email'],
        'smtp.from_name' => fn(ContainerInterface $c) => $c->get('settings')['smtp']['from_name'],
        'proposal.notify_emails' => fn(ContainerInterface $c) => $c->get('settings')['proposal_notify_emails'] ?? '',
        
        // Уведомления о предложениях — PHPMailer
        ProposalNotifierInterface::class => DI\create(PhpMailerProposalNotifier::class)
            ->constructor(
                DI\get('smtp.host'),
                DI\get('smtp.port'),
                DI\get('smtp.user'),
                DI\get('smtp.password'),
                DI\get('smtp.from_email'),
                DI\get('smtp.from_name'),
                DI\get('proposal.notify_emails'),
                DI\get(LoggerInterface::class),
            ),

        // RabbitMQ — строковые параметры из настроек
        'rabbitmq.host' => fn(ContainerInterface $c) => $c->get('settings')['rabbitmq']['host'],
        'rabbitmq.port' => fn(ContainerInterface $c) => $c->get('settings')['rabbitmq']['port'],
        'rabbitmq.login' => fn(ContainerInterface $c) => $c->get('settings')['rabbitmq']['login'],
        'rabbitmq.password' => fn(ContainerInterface $c) => $c->get('settings')['rabbitmq']['password'],
        'rabbitmq.vhost' => fn(ContainerInterface $c) => $c->get('settings')['rabbitmq']['vhost'],

        // RabbitMQ — фабрика подключений
        RabbitMQConnectionFactory::class => DI\create(RabbitMQConnectionFactory::class)
            ->constructor(
                DI\get('rabbitmq.host'),
                DI\get('rabbitmq.port'),
                DI\get('rabbitmq.login'),
                DI\get('rabbitmq.password'),
                DI\get('rabbitmq.vhost'),
            ),
    
        // RabbitMQ — издатель
        QueuePublisherInterface::class => DI\create(RabbitMQPublisher::class)
            ->constructor(
                DI\get(RabbitMQConnectionFactory::class),
                DI\get(LoggerInterface::class),
            ),
            
                // PSR-17 HTTP-фабрики (Guzzle)
                RequestFactoryInterface::class => DI\create(HttpFactory::class),
                StreamFactoryInterface::class => DI\create(HttpFactory::class),
            
                // RAG Search — параметры из настроек
                'rag.search_url' => fn(ContainerInterface $c) => $c->get('settings')['rag']['search_url'],
                'rag.api_key' => fn(ContainerInterface $c) => $c->get('settings')['rag']['api_key'],
                'rag.timeout' => fn(ContainerInterface $c) => $c->get('settings')['rag']['timeout'],
            
                // RAG Search — PSR-18 HTTP-клиент (Guzzle)
                'rag.http_client' => function (ContainerInterface $c) {
                    return new Client([
                        'timeout' => $c->get('rag.timeout'),
                        'connect_timeout' => 10,
                    ]);
                },
            
                // RAG Search — HTTP-клиент к Cloud Function
                RagSearchClientInterface::class => DI\create(RagSearchClient::class)
                    ->constructor(
                        DI\get('rag.search_url'),
                        DI\get('rag.api_key'),
                        DI\get(LoggerInterface::class),
                        DI\get('rag.http_client'),
                        DI\get(RequestFactoryInterface::class),
                        DI\get(StreamFactoryInterface::class),
                    ),
            
                // Сервис отправки главного меню
                MainMenuSender::class => DI\create(MainMenuSender::class)
                    ->constructor(DI\get(MaxBotClientInterface::class)),
            
                // Use Cases — маршрутизатор webhook Max
                HandleMaxWebhook::class => DI\create(HandleMaxWebhook::class)
                    ->constructor(
                        DI\get(HandleChannelMessage::class),
                        DI\get(HandleDialogMessage::class),
                        DI\get(HandleMessageCallback::class),
                        DI\get(HandleBotStarted::class),
                        DI\get(HandleBotStopped::class),
                        DI\get(LoggerInterface::class),
                    ),
            
                HandleChannelMessage::class => DI\create(HandleChannelMessage::class)
                    ->constructor(
                        DI\get(QueuePublisherInterface::class),
                        DI\get(LoggerInterface::class),
                    ),
            
                HandleDialogMessage::class => DI\create(HandleDialogMessage::class)
                    ->constructor(
                        DI\get(ConversationStateRepositoryInterface::class),
                        DI\get(MaxBotClientInterface::class),
                        DI\get(LoggerInterface::class),
                        DI\get(MainMenuSender::class),
                        DI\get(QueuePublisherInterface::class),
                    ),
            
                HandleMessageCallback::class => DI\create(HandleMessageCallback::class)
                    ->constructor(
                        DI\get(ConversationStateRepositoryInterface::class),
                        DI\get(MaxBotClientInterface::class),
                        DI\get(ProcessProposal::class),
                        DI\get(GetContacts::class),
                        DI\get(LoggerInterface::class),
                        DI\get(MainMenuSender::class),
                    ),
            
                HandleBotStarted::class => DI\create(HandleBotStarted::class)
                ->constructor(
                    DI\get(ConversationStateRepositoryInterface::class),
                    DI\get(LoggerInterface::class),
                    DI\get(BotSubscriberRepositoryInterface::class),
                    DI\get(MainMenuSender::class),
                ),
    
        HandleBotStopped::class => DI\create(HandleBotStopped::class)
            ->constructor(
                DI\get(ConversationStateRepositoryInterface::class),
                DI\get(LoggerInterface::class),
                DI\get(BotSubscriberRepositoryInterface::class),
            ),
    
        HandleTelegramWebhook::class => DI\create(HandleTelegramWebhook::class)
            ->constructor(DI\get(LoggerInterface::class)),
    
        ProcessProposal::class => DI\create(ProcessProposal::class)
        ->constructor(
            DI\get(ProposalRepositoryInterface::class),
            DI\get(ProposalNotifierInterface::class),
            DI\get(LoggerInterface::class),
        ),
    
        GetContacts::class => DI\create(GetContacts::class)
        ->constructor(
            DI\get(ContactRepositoryInterface::class),
        ),
    
        ForwardToTelegram::class => DI\create(ForwardToTelegram::class)
        ->constructor(
            DI\get(TelegramBotClientInterface::class),
            DI\get(LoggerInterface::class),
            DI\get('telegram.channel'),
            DI\get('max.channel'),
        ),
    
        ProcessRagQuery::class => DI\create(ProcessRagQuery::class)
            ->constructor(
                DI\get(RagSearchClientInterface::class),
                DI\get(MaxBotClientInterface::class),
                DI\get(RagResponseFormatter::class),
                DI\get(MainMenuSender::class),
                DI\get(LoggerInterface::class),
            ),
    
        // Use Cases — рассылка новостей
        SendNewsToUser::class => DI\create(SendNewsToUser::class)
            ->constructor(
                DI\get(MaxBotClientInterface::class),
                DI\get(NewsDeliveryRepositoryInterface::class),
                DI\get(LoggerInterface::class),
            ),
    
        DeliverNews::class => DI\create(DeliverNews::class)
            ->constructor(
                DI\get(NewsRepositoryInterface::class),
                DI\get(NewsDeliveryRepositoryInterface::class),
                DI\get(BotSubscriberRepositoryInterface::class),
                DI\get(QueuePublisherInterface::class),
                DI\get(LoggerInterface::class),
            ),
    
        // RabbitMQ — consumer дублирования Max->Telegram (webhook-режим)
        TelegramForwardConsumer::class => DI\create(TelegramForwardConsumer::class)
            ->constructor(
                DI\get(RabbitMQConnectionFactory::class),
                DI\get(FallbackMessageRepositoryInterface::class),
                DI\get(LoggerInterface::class),
                DI\get(ForwardToTelegram::class),
                DI\get(MaxApiClient::class), // для fallback getMessageById
                DI\get(DatabaseConnectionInterface::class),
            ),
    
        // Telegram Long Polling — worker получает обновления через getUpdates()
        TelegramLongPollWorker::class => DI\create(TelegramLongPollWorker::class)
            ->constructor(
                DI\get(TelegramWebhookClientInterface::class),
                DI\get(HandleTelegramWebhook::class),
                DI\get(LoggerInterface::class),
            ),
    
        // RabbitMQ — consumer RAG-запросов
        RagQueryConsumer::class => DI\create(RagQueryConsumer::class)
            ->constructor(
                DI\get(RabbitMQConnectionFactory::class),
                DI\get(FallbackMessageRepositoryInterface::class),
                DI\get(LoggerInterface::class),
                DI\get(ProcessRagQuery::class),
                DI\get(DatabaseConnectionInterface::class),
            ),
    
        // RabbitMQ — consumer DLQ-очереди mkd.fallback
        FallbackConsumer::class => DI\create(FallbackConsumer::class)
            ->constructor(
                DI\get(RabbitMQConnectionFactory::class),
                DI\get(FallbackMessageRepositoryInterface::class),
                DI\get(LoggerInterface::class),
                DI\get(DatabaseConnectionInterface::class),
            ),
    
        // RabbitMQ — consumer рассылки новостей
        NewsDeliveryConsumer::class => DI\create(NewsDeliveryConsumer::class)
            ->constructor(
                DI\get(RabbitMQConnectionFactory::class),
                DI\get(FallbackMessageRepositoryInterface::class),
                DI\get(LoggerInterface::class),
                DI\get(SendNewsToUser::class),
                DI\get(DatabaseConnectionInterface::class),
            ),

        // Presentation — маппер
        MaxUpdateMapper::class => DI\create(MaxUpdateMapper::class)
            ->constructor(DI\get(LoggerInterface::class)),

        // Presentation — контроллеры
        MaxWebhookController::class => DI\create(MaxWebhookController::class)
            ->constructor(
                DI\get(MaxUpdateMapper::class),
                DI\get(HandleMaxWebhook::class),
                DI\get(ProcessedWebhookRepositoryInterface::class),
                DI\get(LoggerInterface::class),
            ),

        TelegramWebhookController::class => DI\create(TelegramWebhookController::class)
            ->constructor(DI\get(HandleTelegramWebhook::class)),

        // RabbitMQ — проверка доступности через DI
        RabbitMQConnectionInterface::class => DI\create(RabbitMQConnection::class)
            ->constructor(
                DI\get(RabbitMQConnectionFactory::class),
            ),
    
        HealthController::class => DI\create(HealthController::class)
            ->constructor(DI\get(DatabaseConnectionInterface::class), DI\get(RabbitMQConnectionInterface::class)),

        // Presentation — middleware
        MaxWebhookAuthMiddleware::class => DI\create(MaxWebhookAuthMiddleware::class)
            ->constructor(
                DI\get('max.webhook_secret'),
                DI\get(LoggerInterface::class),
            ),
        TelegramWebhookAuthMiddleware::class => DI\create(TelegramWebhookAuthMiddleware::class)
            ->constructor(
                DI\get('telegram.secret_token'),
                DI\get(LoggerInterface::class),
            ),
];
