<?php

declare(strict_types=1);

namespace App\Infrastructure\Container;

use App\Domain\Service\BankStatementService;
use App\Infrastructure\Repository\FileStatementRequestRepository;
use App\Infrastructure\Queue\RabbitMQQueueService;
use App\Infrastructure\Notification\TelegramNotificationService;
use App\Application\UseCase\CreateStatementRequestUseCase;
use App\Application\UseCase\ProcessStatementRequestUseCase;
use App\Presentation\Controller\StatementRequestController;
use App\Presentation\Controller\TelegramController;
use App\Presentation\Controller\TelegramWebhookController;
use App\Infrastructure\Router\Router;
use App\Infrastructure\Config\AppConfig;
use InvalidArgumentException;

final class ServiceContainer
{
    private static ?self $instance = null;

    private function __construct(
        private array $services = []
    ) {
        AppConfig::load();
        $this->initializeServices();
    }

    /**
     * Возвращает синглтон-экземпляр контейнера
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Инициализирует все сервисы приложения
     */
    private function initializeServices(): void
    {
        // Infrastructure services
        $this->services['repository'] = new FileStatementRequestRepository();
        $this->services['queueService'] = new RabbitMQQueueService();
        $this->services['telegramService'] = new TelegramNotificationService(
            AppConfig::getTelegramBotToken()
        );

        // Domain services
        $this->services['bankStatementService'] = new BankStatementService();

        // Use Cases
        $this->services['createUseCase'] = new CreateStatementRequestUseCase(
            $this->services['repository'],
            $this->services['queueService']
        );
        $this->services['processUseCase'] = new ProcessStatementRequestUseCase(
            $this->services['repository'],
            $this->services['bankStatementService'],
            $this->services['telegramService']
        );

        // Controllers
        $this->services['statementController'] = new StatementRequestController(
            $this->services['createUseCase'],
            $this->services['processUseCase']
        );
        $this->services['telegramController'] = new TelegramController(
            $this->services['telegramService']
        );
        $this->services['webhookController'] = new TelegramWebhookController();

        // Router
        $this->services['router'] = new Router(
            $this->services['statementController'],
            $this->services['telegramController'],
            $this->services['webhookController']
        );
    }

    /**
     * Возвращает сервис по имени
     */
    public function get(string $serviceName)
    {
        if (!isset($this->services[$serviceName])) {
            throw new InvalidArgumentException("Service '{$serviceName}' not found");
        }
        return $this->services[$serviceName];
    }

    /**
     * Возвращает роутер приложения
     */
    public function getRouter(): Router
    {
        return $this->get('router');
    }
}
