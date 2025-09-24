<?php declare(strict_types=1);

namespace Tests\Functional;

use Tests\Support\FunctionalTester;

class EmailValidationCest
{
    public function _before(FunctionalTester $I)
    {
        $I->amOnPage('/');
    }

    public function _after(FunctionalTester $I)
    {
    }

    // tests
    public function tryToTest(FunctionalTester $I)
    {
        $I->see('Email Validator');
        $I->seeElement('textarea#emails');
        $I->seeElement('button');
    }

    public function testEmailValidationWithValidEmail(FunctionalTester $I)
    {
        // Submit form via POST with valid email
        $I->submitForm('form', ['emails' => ['test@gmail.com']], 'Validate Emails');

        // Check that we get results
        $I->see('"success":true');
        $I->see('test@gmail.com');
        $I->see('"valid":true');
    }

    public function testEmailValidationWithInvalidEmail(FunctionalTester $I)
    {
        // Submit form via POST with invalid email
        $I->submitForm('form', ['emails' => ['invalid.email']], 'Validate Emails');
        
        // Check that we get results
        $I->see('"success":true');
        $I->see('invalid.email');
        $I->see('Invalid email format');
    }
}