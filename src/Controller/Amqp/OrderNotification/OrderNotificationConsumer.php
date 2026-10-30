<?php

declare(strict_types=1);

namespace App\Controller\Amqp\OrderNotification;

use App\Domain\OrderNotification\Contract\NotificationTaskWarningListenerInterface;
use App\Domain\OrderNotification\Entity\NotificationTask;
use App\Domain\OrderNotification\Exception\RetryableFailure;
use App\Domain\OrderNotification\Exception\Task\NotificationTaskStorageException;
use App\Domain\OrderNotification\Exception\TerminalFailure;
use App\Domain\OrderNotification\NotificationTaskService;
use App\Domain\OrderNotification\ValueObject\Task\TaskId;
use App\Domain\OrderNotification\ValueObject\Task\TaskStatus;
use App\Infrastructure\RabbitMq\Connection\AmqpConnectionInterface;
use App\Infrastructure\RabbitMq\Consumer\AbstractConsumer;
use App\Infrastructure\RabbitMq\Consumer\HandleResult;
use App\Infrastructure\RabbitMq\Message\NotificationTaskMessage;
use PhpAmqpLib\Message\AMQPMessage;

class OrderNotificationConsumer extends AbstractConsumer
{
    private ?DeliveryContext $context = null;

    private readonly NotificationTaskService $taskService;
    private readonly NotificationTaskWarningListenerInterface $warningListener;

    public function __construct(
        NotificationTaskService $taskService,
        NotificationTaskWarningListenerInterface $warningListener,
        AmqpConnectionInterface $connection,
        string $queueName,
        int $maxAttempts,
        int $prefetchCount,
    ) {
        $this->taskService = $taskService;
        $this->warningListener = $warningListener;
        parent::__construct(
            connection: $connection,
            queueName: $queueName,
            maxAttempts: $maxAttempts,
            prefetchCount: $prefetchCount,
        );
    }

    protected function handle(AMQPMessage $msg): HandleResult
    {
        try {
            $message = $this->parseMessage($msg);
        } catch (\JsonException $e) {
            return HandleResult::drop('Invalid JSON: ' . $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            return HandleResult::drop('Invalid payload: ' . $e->getMessage());
        }

        $retryAgeSeconds = $this->measureRetryAge($msg) ?? 0.0;
        $context = $this->startContext($message, $retryAgeSeconds);

        try {
            $task = $this->taskService->getTask(TaskId::fromInt($message->taskId));
            $context->attachTask($task);

            $this->logReceived($task, $retryAgeSeconds);

            $this->taskService->processTask($task, $this->getMaxAttempts());
            return HandleResult::ack();

        } catch (RetryableFailure $e) {
            $context->rememberError($e);
            return HandleResult::reject($e->getMessage(), $e->getCode());

        } catch (TerminalFailure $e) {
            $context->rememberError($e);
            $this->failSafely($context, $e->getMessage());
            return HandleResult::drop($e->getMessage(), $e->getCode());

        } catch (\Throwable $e) {
            $this->failSafely($context, 'Internal error: ' . $e->getMessage());
            throw $e; // в AbstractConsumer: drop + onError с трейсом
        }
    }

    protected function onDeliveryStart(): void
    {
        $this->resetContext();
    }

    protected function onMessageAcked(): void
    {
        if ($this->context === null) {
            parent::onMessageAcked();
            return;
        }

        fwrite(STDOUT, sprintf(" [ACK] %s\n", $this->taskContext($this->context)));
    }

    protected function onMessageRejected(string $reason, int $code = 0): void
    {
        if ($this->context === null) {
            parent::onMessageRejected($reason, $code);
            return;
        }

        fwrite(STDOUT, sprintf(
            " [REJECT] %s%s | %s\n",
            $this->contextLabel(),
            $this->formatReason($reason, $code),
            $this->contextSummary($this->context),
        ));
    }

    protected function onMessageDropped(string $reason, int $code = 0): void
    {
        if ($this->context === null) {
            parent::onMessageDropped($reason, $code);
            return;
        }

        fwrite(STDOUT, sprintf(
            " [DROP] %s%s | %s\n",
            $this->contextLabel(),
            $this->formatReason($reason, $code),
            $this->contextSummary($this->context),
        ));
    }

    protected function contextLabel(): string
    {
        if ($this->context === null) {
            return parent::contextLabel();
        }

        return $this->errorLabel($this->context);
    }

    private function errorLabel(DeliveryContext $context): string
    {
        $error = $context->getError();

        return ($error !== null) ? sprintf('[%s] ', $this->shortClass($error)) : '';
    }

    /**
     * @throws \JsonException
     * @throws \InvalidArgumentException
     */
    private function parseMessage(AMQPMessage $msg): NotificationTaskMessage
    {
        $data = json_decode($msg->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return NotificationTaskMessage::fromArray($data);
    }

    private function startContext(NotificationTaskMessage $message, float $retryAgeSeconds): DeliveryContext
    {
        $context = new DeliveryContext(
            taskId: $message->taskId,
            startedAt: microtime(true),
            retryAgeSeconds: $retryAgeSeconds,
        );

        $this->context = $context;

        return $context;
    }

    private function resetContext(): void
    {
        $this->context = null;
    }

    private function logReceived(NotificationTask $task, float $retryAgeSeconds): void
    {
        $parts = [
            sprintf('[RECEIVED] (delivery %d)', $this->getDeliveryNumber()),
            sprintf('Task: %d', $task->getId()->value()),
            sprintf('Attempt: %d/%d', $task->getAttempts(), $this->getMaxAttempts()),
            sprintf('Channel: %s', $task->getChannel()->value),
            sprintf('Recipient: %s', $task->getRecipient()->value()),
            $this->retryAgeLabel($retryAgeSeconds),
        ];

        fwrite(STDOUT, ' ' . implode(' | ', $parts) . "\n");
    }

    private function deliveryContext(DeliveryContext $context): string
    {
        return implode(' | ', [
            sprintf('Task: %d', $context->taskId),
            $this->timingContext($context),
        ]);
    }

    private function taskContext(DeliveryContext $context): string
    {
        $task = $context->requireTask();

        return implode(' | ', [
            sprintf('Task: %d', $this->context->taskId),
            sprintf('Attempt: %d/%d', $task->getAttempts(), $this->getMaxAttempts()),
            sprintf('Transitions: %s', $this->formatChain($task)),
            $this->timingContext($context),
        ]);
    }

    private function contextSummary(DeliveryContext $context): string
    {
        return $context->getTask() !== null
            ? $this->taskContext($context)
            : $this->deliveryContext($context);
    }

    private function timingContext(DeliveryContext $context): string
    {
        return implode(' | ', [
            sprintf('Time: %.1f s', $this->elapsedSeconds($context)),
            $this->retryAgeLabel($context->retryAgeSeconds),
        ]);
    }

    private function elapsedSeconds(DeliveryContext $context): float
    {
        return round(microtime(true) - $context->startedAt, 1);
    }

    private function retryAgeLabel(float $retryAgeSeconds): string
    {
        return sprintf('Retry-age: %.1f s', $retryAgeSeconds);
    }

    private function formatChain(NotificationTask $task): string
    {
        return implode(' → ', array_map(
            static fn (TaskStatus $status): string => $status->value,
            $task->getStatusHistory(),
        ));
    }

    private function failSafely(DeliveryContext $context, string $reason): void
    {
        $task = $context->getTask();
        if ($task === null || $task->hasFinalStatus()) {
            return; // Помечать нечего: задачи нет или она уже в финальном статусе.
        }

        try {
            $this->taskService->failTask($task, $reason);
        } catch (NotificationTaskStorageException $e) {
            $this->warningListener->onTerminalMarkFailure($task, $e);
        }
    }
}
