<?php

declare(strict_types=1);

namespace App\Infrastructure\Container;

use App\Application\Interfaces\ValidateEmailsUseCaseInterface;
use App\Application\UseCases\ValidateEmailsUseCase;
use App\Domain\Interfaces\EmailValidatorInterface;
use App\Domain\Validators\EmailValidator;
use App\Presentation\Controllers\Actions\ValidateEmailsAction;
use App\Presentation\Controllers\Controller;

class ContainerBuilder
{
    public static function build(): Container
    {
        $container = new Container();

        $container->singleton(EmailValidatorInterface::class, function () {
            return new EmailValidator();
        });

        $container->singleton(ValidateEmailsUseCaseInterface::class, function (Container $c) {
            return new ValidateEmailsUseCase(
                $c->get(EmailValidatorInterface::class)
            );
        });

        $container->set(ValidateEmailsAction::class, function (Container $c) {
            return new ValidateEmailsAction(
                $c->get(ValidateEmailsUseCaseInterface::class)
            );
        });

        $container->set(Controller::class, function (Container $c) {
            return new Controller([
                $c->get(ValidateEmailsAction::class),
            ]);
        });

        return $container;
    }
}
