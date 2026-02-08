<?php

declare(strict_types=1);

namespace Tests\Codeception\Support;

use Codeception\Actor;

class ApiTester extends Actor
{
    use _generated\ApiTesterActions;

    /**
     * Отправляет POST запрос с JSON телом для валидации email
     */
    public function sendEmailsForValidation(array $emails): void
    {
        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->sendPost('/emails', $emails);
    }

    /**
     * Проверяет успешный ответ с данными
     */
    public function seeSuccessResponse(): void
    {
        $this->seeResponseCodeIs(200);
        $this->seeResponseIsJson();
        $this->seeResponseContainsJson(['success' => true]);
    }

    /**
     * Проверяет ответ с ошибкой
     */
    public function seeErrorResponse(int $statusCode = 400): void
    {
        $this->seeResponseCodeIs($statusCode);
        $this->seeResponseIsJson();
        $this->seeResponseContainsJson(['success' => false]);
    }
}
