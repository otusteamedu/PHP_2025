<?php

declare(strict_types=1);

namespace App\Infrastructure\DI;

use App\Application\VerificationEmailService;
use App\Domain\Interfaces\ConfigInterface;
use App\Infrastructure\CheckedEmailsFileWriter;
use App\Infrastructure\Config\EnvConfig;
use App\Infrastructure\DnsEmailVerifier;
use App\Infrastructure\EmailsFileReader;
use App\UserInterface\VerificationEmailCommand;

class ContainerBuilder
{
    public static function build(): Container
    {
        $container = new Container();

        $container->singleton(ConfigInterface::class, function () {
            return new EnvConfig();
        });

        $config = $container->get(ConfigInterface::class);
        $emailsFilePath = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . $config->get('EMAILS_FILE');
        $checkedEmailsFilePath = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . $config->get('CHECKED_EMAILS_FILE');

        $container->singleton(EmailsFileReader::class, function () use ($emailsFilePath) {
            return new EmailsFileReader($emailsFilePath);
        });

        $container->singleton(CheckedEmailsFileWriter::class, function () use ($checkedEmailsFilePath) {
            return new CheckedEmailsFileWriter($checkedEmailsFilePath);
        });

        $container->singleton(DnsEmailVerifier::class, function () {
            return new DnsEmailVerifier();
        });

        $container->set(VerificationEmailService::class, function (Container $container) {
            return new VerificationEmailService(
                $container->get(CheckedEmailsFileWriter::class),
                $container->get(EmailsFileReader::class),
                $container->get(DnsEmailVerifier::class)
            );
        });

        $container->set(VerificationEmailCommand::class, function (Container $container) {
            return new VerificationEmailCommand(
                $container->get(VerificationEmailService::class)
            );
        });

        return $container;
    }
}
