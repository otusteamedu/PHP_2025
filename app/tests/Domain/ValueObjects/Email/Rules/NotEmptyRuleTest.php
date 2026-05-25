<?php

namespace Pryaniki\Tests\Domain\ValueObjects\Email\Rules;

use PHPUnit\Framework\TestCase;
use Pryaniki\App\Domain\ValueObjects\Email\Rules\NotEmptyRule;

class NotEmptyRuleTest extends TestCase
{
    public function testReturnsInvalidForEmptyString(): void
    {
        $rule = new NotEmptyRule();

        $result = $rule->validate('');

        $errors = $result->getErrors();

        $isCorrectErrorMessage = count($errors) === 1 && $errors[0] === 'Field email is empty';

        $this->assertFalse($result->isValid() && $isCorrectErrorMessage);
    }

    public function testReturnsValidForNonEmptyString(): void
    {
        $rule = new NotEmptyRule();

        $result = $rule->validate('test@test.com');

        $errors = $result->getErrors();

        $this->assertTrue($result->isValid() && $errors === []);
    }
}