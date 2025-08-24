<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use App\Application\DTO\EmailValidationRequest;
use App\Application\UseCase\ValidateEmailsUseCase;
use App\Infrastructure\Http\Request\HttpRequest;

final class EmailValidationController
{
    public function __construct(
        private ValidateEmailsUseCase $useCase
    ) {}

    /**
     * Обрабатывает HTTP запрос
     */
    public function handle(HttpRequest $request): string
    {
        $validationRequest = new EmailValidationRequest(
            $request->getParameter('emails')
        );

        return $this->useCase->execute($validationRequest)->getFormattedResult();
    }
}
