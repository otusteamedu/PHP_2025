<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\EmailValidationRequest;
use App\Application\DTO\EmailValidationResponse;
use App\Application\Service\InputParserService;
use App\Application\Service\ResultFormatterService;
use App\Domain\Email\EmailValidationService;

final class ValidateEmailsUseCase
{
    public function __construct(
        private EmailValidationService $validationService,
        private InputParserService $inputParser,
        private ResultFormatterService $resultFormatter
    ) {}

    /**
     * Выполняет сценарий валидации email адресов
     */
    public function execute(EmailValidationRequest $request): EmailValidationResponse
    {
        if ($request->isEmpty()) {
            return new EmailValidationResponse(
                $this->resultFormatter->formatUsageHint()
            );
        }

        $emails = $this->inputParser->parse($request->getRawInput());
        $results = $this->validationService->validateList($emails);

        return new EmailValidationResponse(
            $this->resultFormatter->format($results)
        );
    }
}
