<?php

declare(strict_types=1);

namespace Tests\Codeception\Api;

use Tests\Codeception\Support\ApiTester;

class EmailValidationCest
{
    public function _before(ApiTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
    }

    // Успешные сценарии

    public function validateSingleValidEmail(ApiTester $I): void
    {
        $I->sendEmailsForValidation(['test@example.com']);

        $I->seeSuccessResponse();
        $I->seeResponseContainsJson([
            'data' => [
                [
                    'email' => 'test@example.com',
                    'is_valid' => true
                ]
            ]
        ]);
    }

    public function validateMultipleValidEmails(ApiTester $I): void
    {
        $I->sendEmailsForValidation([
            'user1@gmail.com',
            'user2@yahoo.com'
        ]);

        $I->seeSuccessResponse();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
            'data' => 'array'
        ]);
    }

    public function validateInvalidEmailFormat(ApiTester $I): void
    {
        $I->sendEmailsForValidation(['not-an-email']);

        $I->seeSuccessResponse();
        $I->seeResponseContainsJson([
            'data' => [
                [
                    'email' => 'not-an-email',
                    'is_valid' => false
                ]
            ]
        ]);
    }

    public function validateMixedEmails(ApiTester $I): void
    {
        $I->sendEmailsForValidation([
            'valid@gmail.com',
            'invalid-email',
            'another@yahoo.com'
        ]);

        $I->seeSuccessResponse();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
            'data' => 'array'
        ]);
    }

    // Негативные сценарии

    public function sendEmptyBody(ApiTester $I): void
    {
        $I->sendEmailsForValidation([]);

        $I->seeErrorResponse(400);
        $I->seeResponseContainsJson(['success' => false]);
    }

    public function useWrongHttpMethod(ApiTester $I): void
    {
        $I->sendGet('/emails');

        $I->seeErrorResponse(404);
    }

    public function accessUnknownEndpoint(ApiTester $I): void
    {
        $I->sendPost('/unknown-endpoint', ['test@example.com']);

        $I->seeErrorResponse(404);
    }

    // Граничные случаи

    public function validateEmailWithSubdomain(ApiTester $I): void
    {
        $I->sendEmailsForValidation(['user@mail.subdomain.example.com']);

        $I->seeSuccessResponse();
    }

    public function validateEmailWithPlusSign(ApiTester $I): void
    {
        $I->sendEmailsForValidation(['user+tag@gmail.com']);

        $I->seeSuccessResponse();
    }

    public function validateEmailWithNumbers(ApiTester $I): void
    {
        $I->sendEmailsForValidation(['user123@example123.com']);

        $I->seeSuccessResponse();
    }

    public function validateLargeEmailBatch(ApiTester $I): void
    {
        $emails = [];
        for ($i = 1; $i <= 10; $i++) {
            $emails[] = "user{$i}@example.com";
        }

        $I->sendEmailsForValidation($emails);

        $I->seeSuccessResponse();
    }

    // Проверка структуры ответа

    public function checkResponseStructureForValidEmail(ApiTester $I): void
    {
        $I->sendEmailsForValidation(['test@gmail.com']);

        $I->seeSuccessResponse();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
            'data' => [
                [
                    'email' => 'string',
                    'is_valid' => 'boolean'
                ]
            ]
        ]);
    }

    public function checkResponseStructureForInvalidEmail(ApiTester $I): void
    {
        $I->sendEmailsForValidation(['invalid']);

        $I->seeSuccessResponse();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
            'data' => [
                [
                    'email' => 'string',
                    'is_valid' => 'boolean',
                    'error' => 'string'
                ]
            ]
        ]);
    }
}
