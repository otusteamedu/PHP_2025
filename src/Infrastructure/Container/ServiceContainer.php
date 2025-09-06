<?php

declare(strict_types=1);

namespace App\Infrastructure\Container;

use App\Application\Interface\QueueServiceInterface;
use App\Application\Interface\RequestRepositoryInterface;
use App\Application\UseCase\CreateRequestUseCase;
use App\Application\UseCase\GetRequestStatusUseCase;
use App\Application\UseCase\ProcessRequestUseCase;
use App\Domain\Service\RequestProcessingService;
use App\Infrastructure\Queue\RabbitMQQueueService;
use App\Infrastructure\Repository\FileRequestRepository;
use App\Infrastructure\Router\ApiRouter;
use InvalidArgumentException;

final class ServiceContainer
{
    private static ?self $instance = null;
    private array $services = [];

    private function __construct()
    {
        $this->initializeServices();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get(string $serviceName): mixed
    {
        if (!isset($this->services[$serviceName])) {
            throw new InvalidArgumentException("Service '{$serviceName}' not found");
        }

        return $this->services[$serviceName];
    }

    public function getApiRouter(): ApiRouter
    {
        return $this->get(ApiRouter::class);
    }

    /**
     * Инициализирует все сервисы приложения и их зависимости
     */
    private function initializeServices(): void
    {
        // Infrastructure services
        $this->services[RequestRepositoryInterface::class] = new FileRequestRepository();
        $this->services[QueueServiceInterface::class] = new RabbitMQQueueService();

        // Domain services
        $this->services[RequestProcessingService::class] = new RequestProcessingService();

        // Application services
        $this->services[CreateRequestUseCase::class] = new CreateRequestUseCase(
            $this->services[RequestRepositoryInterface::class],
            $this->services[QueueServiceInterface::class]
        );

        $this->services[GetRequestStatusUseCase::class] = new GetRequestStatusUseCase(
            $this->services[RequestRepositoryInterface::class]
        );

        $this->services[ProcessRequestUseCase::class] = new ProcessRequestUseCase(
            $this->services[RequestRepositoryInterface::class],
            $this->services[RequestProcessingService::class]
        );

        // Infrastructure services (router)
        $this->services[ApiRouter::class] = new ApiRouter(
            $this->services[CreateRequestUseCase::class],
            $this->services[GetRequestStatusUseCase::class]
        );
    }
}
