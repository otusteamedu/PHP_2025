<?php
namespace Pryaniki\App\Application\UseCases;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Pryaniki\App\Domain\Interfaces\DomainExistenceCheckerInterface;
use Pryaniki\App\Domain\ValueObjects\Email\EmailValidatorInterface;
use Pryaniki\App\Domain\ValueObjects\Email\ValidationResult;


class ValidateEmailUseCaseTest extends TestCase
{

    /**
     * @throws Exception
     */
    public function testReturnsSuccessfulResponseForValidEmail(): void
    {
        // Arrange
        $validationResult = new ValidationResult();
        $validationResult->setIsValid(true);

        $validator = $this->createMock(
            EmailValidatorInterface::class,
        );

        $validator
            ->expects(self::once())
            ->method('validate')
            ->with('test@gmail.com')
            ->willReturn($validationResult);

        $dnsChecker = $this->createMock(
            DomainExistenceCheckerInterface::class,
        );

        $dnsChecker
            ->expects(self::once())
            ->method('isExistsDomain')
            ->with('gmail.com')
            ->willReturn(true);

        $useCase = new ValidateEmailUseCase(
            $validator,
            $dnsChecker,
        );

        // Act
        $resultDto = $useCase->execute('test@gmail.com');

        // Assert
        self::assertTrue($resultDto->success);

        self::assertEmpty($resultDto->error);
    }

    /**
     * @throws Exception
     */
    public function testReturnsValidationErrors(): void
    {
        // Arrange
        $validationResult = new ValidationResult();
        $validationResult->setIsValid(false);
        $validationResult->addError('Invalid email');

        $validator = $this->createMock(
            EmailValidatorInterface::class,
        );

        $validator
            ->method('validate')
            ->willReturn($validationResult);

        $dnsChecker = $this->createMock(
            DomainExistenceCheckerInterface::class,
        );

        $dnsChecker
            ->method('isExistsDomain')
            ->willReturn(true);

        $useCase = new ValidateEmailUseCase(
            $validator,
            $dnsChecker,
        );

        // Act
        $resultDto = $useCase->execute('invalid-email');

        // Assert
        self::assertFalse($resultDto->success);

        self::assertContains(
            'Invalid email',
            $resultDto->error,
        );
    }

    /**
     * @throws Exception
     */
    public function testAddsDnsErrorWhenDomainDoesNotExist(): void
    {
        // Arrange
        $validationResult = new ValidationResult();
        $validationResult->setIsValid(true);

        $validator = $this->createMock(
            EmailValidatorInterface::class,
        );

        $validator
            ->method('validate')
            ->willReturn($validationResult);

        $dnsChecker = $this->createMock(
            DomainExistenceCheckerInterface::class,
        );

        $dnsChecker
            ->method('isExistsDomain')
            ->willReturn(false);

        $useCase = new ValidateEmailUseCase(
            $validator,
            $dnsChecker,
        );

        // Act
        $response = $useCase->execute(
            'test@unknown-domain.com',
        );

        // Assert
        self::assertFalse($response->success);

        self::assertContains(
            'DNS record not found',
            $response->error,
        );
    }
}