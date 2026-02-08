<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Container;

use App\Application\Interfaces\ValidateEmailsUseCaseInterface;
use App\Application\UseCases\ValidateEmailsUseCase;
use App\Domain\Validators\EmailValidator;
use App\Infrastructure\Container\Container;
use App\Infrastructure\Container\ContainerBuilder;
use App\Presentation\Controllers\Actions\ValidateEmailsAction;
use App\Presentation\Controllers\Controller;
use PHPUnit\Framework\TestCase;

class ContainerBuilderTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = ContainerBuilder::build();
    }

    public function testBuildReturnsContainer(): void
    {
        $this->assertInstanceOf(Container::class, $this->container);
    }

    public function testContainerHasEmailValidator(): void
    {
        $this->assertTrue($this->container->has(EmailValidator::class));
    }

    public function testContainerHasValidateEmailsUseCaseInterface(): void
    {
        $this->assertTrue($this->container->has(ValidateEmailsUseCaseInterface::class));
    }

    public function testContainerHasValidateEmailsAction(): void
    {
        $this->assertTrue($this->container->has(ValidateEmailsAction::class));
    }

    public function testContainerHasController(): void
    {
        $this->assertTrue($this->container->has(Controller::class));
    }

    public function testEmailValidatorIsSingleton(): void
    {
        $first = $this->container->get(EmailValidator::class);
        $second = $this->container->get(EmailValidator::class);

        $this->assertSame($first, $second);
    }

    public function testValidateEmailsUseCaseIsSingleton(): void
    {
        $first = $this->container->get(ValidateEmailsUseCaseInterface::class);
        $second = $this->container->get(ValidateEmailsUseCaseInterface::class);

        $this->assertSame($first, $second);
    }

    public function testDependencyInjectionChainWorks(): void
    {
        $controller = $this->container->get(Controller::class);

        $this->assertInstanceOf(Controller::class, $controller);

        $action = $this->container->get(ValidateEmailsAction::class);
        $useCase = $this->container->get(ValidateEmailsUseCaseInterface::class);
        $validator = $this->container->get(EmailValidator::class);

        $this->assertInstanceOf(ValidateEmailsAction::class, $action);
        $this->assertInstanceOf(ValidateEmailsUseCase::class, $useCase);
        $this->assertInstanceOf(EmailValidator::class, $validator);
    }
}
