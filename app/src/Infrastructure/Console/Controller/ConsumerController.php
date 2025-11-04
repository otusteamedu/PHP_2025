<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Controller;

use App\Application\DTO\BankStatementMessage;
use App\Application\UseCase\Consume\ConsumeUseCase;
use App\Infrastructure\Notification\EmailNotification;
use App\Infrastructure\Rabbit\Config;
use App\Infrastructure\Rabbit\Consumer\Consumer;
use Exception;

/**
 * Class ConsumerController
 * @package App\Infrastructure\Console\Controller
 */
class ConsumerController
{
    /**
     * @return void
     * @throws Exception
     */
    public function actionConsume(): void
    {
        $consumer = new Consumer(new Config());
        $consumeUseCase = new ConsumeUseCase($consumer, new EmailNotification());

        $callback = function (BankStatementMessage $bankStatementMessage) {
            $message = sprintf(
                'New bank statement request: email = %s, start date =%s, end date =%s',
                $bankStatementMessage->getEmail(),
                $bankStatementMessage->getStartDate(),
                $bankStatementMessage->getEndDate()
            );

            print($message . PHP_EOL);
        };

        $consumeUseCase($callback);
    }
}
