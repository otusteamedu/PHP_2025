<?php declare(strict_types=1);

namespace Tests\Api;

use Tests\Support\ApiTester;

class EmailValidationCest
{
    public function _before(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
    }

    public function _after(ApiTester $I)
    {
    }

    // tests
    public function testValidEmail(ApiTester $I)
    {
        $I->sendPost('/api.php', ['emails' => ['test@gmail.com']]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => true
        ]);
        $I->seeResponseJsonMatchesJsonPath('$.data["test@gmail.com"].valid');
        $I->seeResponseJsonMatchesJsonPath('$.data["test@gmail.com"].regex_check');
        $I->seeResponseJsonMatchesJsonPath('$.data["test@gmail.com"].mx_check');
    }

    public function testInvalidEmail(ApiTester $I)
    {
        $I->sendPost('/api.php', ['emails' => ['invalid.email']]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => true
        ]);
        $I->seeResponseJsonMatchesJsonPath('$.data["invalid.email"].valid');
        $I->seeResponseJsonMatchesJsonPath('$.data["invalid.email"].regex_check');
        $I->seeResponseJsonMatchesJsonPath('$.data["invalid.email"].reason');
    }

    public function testMultipleEmails(ApiTester $I)
    {
        $I->sendPost('/api.php', [
            'emails' => [
                'test@gmail.com',
                'invalid.email',
                'user@yahoo.com'
            ]
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => true
        ]);
        
        // Check that all emails are in the response
        $I->seeResponseJsonMatchesJsonPath('$.data["test@gmail.com"]');
        $I->seeResponseJsonMatchesJsonPath('$.data["invalid.email"]');
        $I->seeResponseJsonMatchesJsonPath('$.data["user@yahoo.com"]');
    }

    public function testInvalidMethod(ApiTester $I)
    {
        $I->sendGet('/api.php');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::METHOD_NOT_ALLOWED);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error' => 'Method not allowed. Use POST.'
        ]);
    }

    public function testInvalidInput(ApiTester $I)
    {
        $I->sendPost('/api.php', ['invalid' => 'data']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error' => 'Invalid input. Please provide an array of emails.'
        ]);
    }
}