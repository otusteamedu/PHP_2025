<?php
declare(strict_types=1);

namespace App\Infrastructure\Container;

use App\Application\UseCases\VerifyEmailsUseCase;
use App\Domain\Interfaces\EmailValidatorInterface;
use App\Domain\Validators\EmailFormatValidator;
use App\Domain\Validators\EmailMxRecordValidator;
use App\Domain\Validators\EmailValidator;
use App\Presentation\Actions\TemplateRenderAction;
use App\Presentation\Actions\VerifyEmailsAction;
use App\Presentation\Controllers\App;
use App\Presentation\Controllers\EmailVerificationController;
use App\Presentation\Views\ViewRenderer;

class ContainerBuilder
{
    public static function build(): Container
    {
        $container = new Container();

        $container->singleton(EmailValidatorInterface::class, function () {
            return new EmailValidator(new EmailFormatValidator(), new EmailMxRecordValidator());
        });

        $container->set(VerifyEmailsUseCase::class, function (Container $c) {
            return new VerifyEmailsUseCase(
                $c->get(EmailValidatorInterface::class)
            );
        });

        $container->set(ViewRenderer::class, function (Container $c) {
            $templatePath = dirname(__DIR__, 2) . '/Presentation/Views/Templates/';
            return new ViewRenderer($templatePath);
        });

        $container->set(VerifyEmailsAction::class, function (Container $c) {
            return new VerifyEmailsAction(
                $c->get(VerifyEmailsUseCase::class)
            );
        });

        $container->set(TemplateRenderAction::class, function (Container $c) {
            return new TemplateRenderAction(
                $c->get(ViewRenderer::class)
            );
        });

        $container->set(EmailVerificationController::class, function (Container $c) {
            return new EmailVerificationController([
                $c->get(TemplateRenderAction::class),
                $c->get(VerifyEmailsAction::class)
            ]);
        });

        $container->set(App::class, function (Container $c) {
            return new App(
                $c->get(EmailVerificationController::class)
            );
        });

        return $container;
    }
}
