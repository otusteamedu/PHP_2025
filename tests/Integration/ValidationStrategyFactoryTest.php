<?php

namespace EmailsVerifier\Tests\Integration;

use EmailsVerifier\Application\Services\CompositeValidator;
use EmailsVerifier\Domain\Email;
use EmailsVerifier\Infrastructure\ValidationStrategyFactory;
use PHPUnit\Framework\TestCase;
use EmailsVerifier\Tests\ValidationErrorHelper;

class ValidationStrategyFactoryTest extends TestCase
{
    public function testCreateStrategyReturnsCompositeValidatorWithAllValidators(): void
    {
        $strategy = ValidationStrategyFactory::createStrategy();

        $this->assertInstanceOf(CompositeValidator::class, $strategy);

        $formatErrors = $strategy->validate(new Email('invalid-format'));
        $this->assertNotEmpty($formatErrors);
        $this->assertContains('Адрес не соответствует шаблону', ValidationErrorHelper::getErrorMessages($formatErrors));

        $mxErrors = $strategy->validate(new Email('test@invalid-nonexistent-domain-abc.ru'));
        $this->assertNotEmpty($mxErrors);
        $this->assertContains('MX-запись не найдена', ValidationErrorHelper::getErrorMessages($mxErrors));
    }
}
