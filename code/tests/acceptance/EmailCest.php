<?php
declare(strict_types=1);

namespace Ak\Hw\Tests\_support\acceptance;

use Ak\Hw\Tests\AcceptanceTester;
use Codeception\Util\HttpCode;

class EmailCest
{
    /**
     * Test that the API returns an error when the text parameter is missing.
     *
     * @param AcceptanceTester $I
     */
    public function testMissingTextParameter(AcceptanceTester $I): void
    {
        $I->sendAjaxPostRequest('/', ['text' => '']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success' => false, 'error' => ['message' => 'Text parameter is required.']]);
    }

    /**
     * Test that the API finds emails in the given text.
     *
     * @param AcceptanceTester $I
     */
    public function testFindsEmails(AcceptanceTester $I): void
    {
        $text = 'This is a test with some emails: test1@example.com and test2@example.com.';
        $I->sendAjaxPostRequest('/', ['text' => $text]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => true,
            'data' => [
                'emails' => [
                    'test1@example.com',
                    'test2@example.com'
                ]
            ]
        ]);
    }

    /**
     * Test that the API returns an empty array when no emails are found.
     *
     * @param AcceptanceTester $I
     */
    public function testNoEmailsFound(AcceptanceTester $I): void
    {
        $text = 'This is a test with no emails.';
        $I->sendAjaxPostRequest('/', ['text' => $text]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => true,
            'data' => [
                'emails' => []
            ]
        ]);
    }
}
