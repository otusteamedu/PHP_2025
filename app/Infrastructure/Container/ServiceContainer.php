<?php

declare(strict_types=1);

namespace App\Infrastructure\Container;

use App\Application\Service\InputParserService;
use App\Application\Service\ResultFormatterService;
use App\Application\UseCase\ValidateEmailsUseCase;
use App\Domain\Email\EmailValidationService;
use App\Infrastructure\Http\Controller\EmailValidationController;
use App\Infrastructure\Http\Request\HttpRequest;
use App\Infrastructure\Validator\DnsMxValidator;
use App\Infrastructure\Validator\EmailFormatValidator;

final class ServiceContainer
{
    private array $services = [];

    /**
     * Получает сервис из контейнера
     */
    public function get(string $id): object
    {
        if (!isset($this->services[$id])) {
            $this->services[$id] = $this->create($id);
        }

        return $this->services[$id];
    }

    /**
     * Создает экземпляр сервиса
     */
    private function create(string $id): object
    {
        return match ($id) {
            EmailValidationController::class => $this->createEmailValidationController(),
            ValidateEmailsUseCase::class => $this->createValidateEmailsUseCase(),
            EmailValidationService::class => $this->createEmailValidationService(),
            InputParserService::class => new InputParserService(),
            ResultFormatterService::class => new ResultFormatterService(),
            HttpRequest::class => new HttpRequest(),
            EmailFormatValidator::class => new EmailFormatValidator(),
            DnsMxValidator::class => new DnsMxValidator(),
            default => throw new \InvalidArgumentException("Unknown service: $id")
        };
    }

    /**
     * Создает EmailValidationController с зависимостями
     */
    private function createEmailValidationController(): EmailValidationController
    {
        return new EmailValidationController(
            $this->get(ValidateEmailsUseCase::class)
        );
    }

    /**
     * Создает ValidateEmailsUseCase с зависимостями
     */
    private function createValidateEmailsUseCase(): ValidateEmailsUseCase
    {
        return new ValidateEmailsUseCase(
            $this->get(EmailValidationService::class),
            $this->get(InputParserService::class),
            $this->get(ResultFormatterService::class)
        );
    }

    /**
     * Создает EmailValidationService с цепочкой валидаторов
     */
    private function createEmailValidationService(): EmailValidationService
    {
        $service = new EmailValidationService();
        
        return $service
            ->addValidator($this->get(EmailFormatValidator::class))
            ->addValidator($this->get(DnsMxValidator::class));
    }
}
