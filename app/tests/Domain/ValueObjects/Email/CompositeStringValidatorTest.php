<?php

declare(strict_types=1);

namespace Pryaniki\Tests\Domain\ValueObjects\Email;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Pryaniki\App\Domain\ValueObjects\Email\CompositeStringValidator;
use Pryaniki\App\Domain\ValueObjects\Email\Rules\EmailRuleInterface;
use Pryaniki\App\Domain\ValueObjects\Email\ValidationResult;

class CompositeStringValidatorTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testReturnsValidWhenAllRulesPass(): void
    {
        // Arrange
        $successValidationResult = new ValidationResult();
        $successValidationResult->setIsValid(true);

        $rule = self::createMock(EmailRuleInterface::class);

        $rule->method('validate')->willReturn($successValidationResult);

        $validator = new CompositeStringValidator([$rule, $rule, $rule]);

        // Act

        $result = $validator->validate('test@gmail.com');

        // Assert

        self::assertTrue($result->isValid());

        self::assertEmpty($result->getErrors());
    }
}