<?php declare(strict_types=1);

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class EmailValidationCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
    }

    public function _after(AcceptanceTester $I): void
    {
    }

    // tests
    public function tryToTest(AcceptanceTester $I): void
    {
        $I->see('Email Validator');
        $I->seeElement('textarea[name="emails"]');
        $I->seeElement('button[type="submit"]');
    }

    public function testValidEmailValidation(AcceptanceTester $I): void
    {
        $I->seeElement('textarea[name="emails"]');
        $I->fillField('textarea[name="emails"]', 'test@gmail.com');
        $I->click('button[type="submit"]');
        $I->waitForElementVisible('#results', 10);
        $I->see('Validation Results');
        $I->see('test@gmail.com');
        $I->see('Valid', '.valid');
    }

    public function testMultipleEmailsValidation(AcceptanceTester $I): void
    {
        $I->fillField('textarea[name="emails"]', "test@gmail.com;invalid.email");
        $I->click('button[type="submit"]');
        $I->waitForElementVisible('#results', 10);
        $I->see('Validation Results');
        $I->see('test@gmail.com');
        $I->see('invalid.email');
        $I->see('Valid', '.valid');
        $I->see('Invalid', '.invalid');
    }

    public function testInvalidEmailValidation(AcceptanceTester $I): void
    {
        $I->fillField('textarea[name="emails"]', 'invalid.email');
        $I->click('button[type="submit"]');
        $I->waitForElementVisible('#results', 10);
        $I->see('Validation Results');
        $I->see('invalid.email');
        $I->see('Invalid', '.invalid');
    }

    public function testInvalidInputValidation(AcceptanceTester $I): void
    {
        $I->see('Email Validator');
        $I->fillField('textarea[name="emails"]', "invalid.email\n\ntest@gmail.com");
        $I->click('button[type="submit"]');
        $I->waitForElementVisible('#results', 10);
        $I->see('invalid.email test@gmail.com: Invalid');
        $I->see('Invalid email format');
    }
}