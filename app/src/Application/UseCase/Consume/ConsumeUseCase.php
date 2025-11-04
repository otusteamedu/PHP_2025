<?php

declare(strict_types=1);

namespace App\Application\UseCase\Consume;

use App\Application\Consumer\ConsumerInterface;
use App\Application\DTO\BankStatementMessage;
use App\Application\Notification\NotificationInterface;
use App\Infrastructure\Notification\EmailNotificationMessage;
use DomainException;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class ConsumeUseCase
 * @package App\Application\UseCase\Consume
 */
readonly class ConsumeUseCase
{
    /**
     * @param ConsumerInterface $consumer
     * @param NotificationInterface $notification
     */
    public function __construct(
        private ConsumerInterface $consumer,
        private NotificationInterface $notification,
    ) {
    }

    /**
     * @param callable $callback
     * @return void
     */
    public function __invoke(callable $callback): void
    {
        $callbackWithNotification = function (AMQPMessage $msg) use ($callback) {
            $decodedMessage = json_decode($msg->getBody(), true);
            $bankStatementMessage = new BankStatementMessage(
                $decodedMessage['email'],
                $decodedMessage['startDate'],
                $decodedMessage['endDate'],
            );

            try {
                $notificationMessage = $this->createNotificationMessage($bankStatementMessage);
                $this->notification->send($notificationMessage);
                print('Notification successfully sent.' . PHP_EOL);
            } catch (DomainException $e) {
                print($e->getMessage() . PHP_EOL);
            }

            $callback($bankStatementMessage);
        };

        $this->consumer->consume($callbackWithNotification);
    }

    /**
     * @param BankStatementMessage $bankStatementMessage
     * @return EmailNotificationMessage
     */
    private function createNotificationMessage(BankStatementMessage $bankStatementMessage): EmailNotificationMessage
    {
        $notificationMessageSubject = 'Bank statement';
        $notificationMessageText = sprintf(
            'Your bank statement from %s to %s is ready.',
            $bankStatementMessage->getStartDate(),
            $bankStatementMessage->getEndDate()
        );

        return new EmailNotificationMessage(
            $bankStatementMessage->getEmail(),
            $notificationMessageSubject,
            $notificationMessageText
        );
    }
}
