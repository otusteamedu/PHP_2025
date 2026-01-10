<?php

namespace Consumer;

use Consumer\Application\BankDetail\BankDetailUseCase;
use Consumer\Application\Mailer\PhpMailerService;
use Consumer\Application\Queue\RabbitMQ;
use Consumer\Infrastructure\BankDetail\PhpMailerMail;
use Throwable;

class Kernel
{
    /**
     * Прослушка сообщений и отправка письма
     *
     * @return void
     */
    public function run(): void {
        try {
            (new BankDetailUseCase(
                new PhpMailerMail(new PhpMailerService()),
                new RabbitMQ()
            ))->run();
        } catch (Throwable $e) {
            echo $e->getMessage();
        }
    }
}