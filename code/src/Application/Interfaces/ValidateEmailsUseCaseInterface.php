<?php

declare(strict_types=1);

namespace App\Application\Interfaces;

use App\Domain\DTO\EmailValidationRequest;
use App\Domain\DTO\EmailValidationResult;

interface ValidateEmailsUseCaseInterface
{
    /**
     * Выполняет валидацию списка email-адресов
     * 
     * @param EmailValidationRequest $request DTO с email-адресами
     * @return EmailValidationResult[] Массив результатов валидации
     */
    public function execute(EmailValidationRequest $request): array;
}
