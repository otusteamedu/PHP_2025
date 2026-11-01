<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Entity;

use App\Domain\OrderNotification\Exception\Delivery\MaxAttemptsExceededException;
use App\Domain\OrderNotification\Exception\Task\InvalidTaskStateException;
use App\Domain\OrderNotification\ValueObject\Order\OrderData;
use App\Domain\OrderNotification\ValueObject\Order\OrderEvent;
use App\Domain\OrderNotification\ValueObject\Order\OrderId;
use App\Domain\OrderNotification\ValueObject\Recipient\RecipientInterface;
use App\Domain\OrderNotification\ValueObject\Task\NotificationChannel;
use App\Domain\OrderNotification\ValueObject\Task\TaskId;
use App\Domain\OrderNotification\ValueObject\Task\TaskStatus;

class NotificationTask
{
    private ?TaskId $id = null;
    private ?OrderData $orderData = null;
    private int $attempts = 0;

    /**
     * @var list<TaskStatus>
     * История переходов текущей попытки в порядке наступления.
     * При создании задачи начинается с Pending.
     */
    private array $statusHistory;

    public function __construct(
        private readonly OrderId $orderId,
        private readonly OrderEvent $orderEvent,
        private readonly NotificationChannel $channel,
        private readonly RecipientInterface $recipient,
        private TaskStatus $status = TaskStatus::Pending,
        private readonly \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
        private ?\DateTimeImmutable $processedAt = null,
        private ?string $failureReason = null,
    ) {
        $this->recordTransition();
    }

    public function getId(): ?TaskId { return $this->id; }
    public function getOrderId(): OrderId { return $this->orderId; }
    public function getOrderEvent(): OrderEvent { return $this->orderEvent; }
    public function getOrderData(): ?OrderData { return $this->orderData; }
    public function getChannel(): NotificationChannel { return $this->channel; }
    public function getRecipient(): RecipientInterface { return $this->recipient; }
    public function getStatus(): TaskStatus { return $this->status; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getProcessedAt(): ?\DateTimeImmutable { return $this->processedAt; }
    public function getAttempts(): int { return $this->attempts; }
    public function getStatusHistory(): array { return $this->statusHistory; }
    public function getFailureReason(): ?string { return $this->failureReason; }

    public static function create(
        OrderId $orderId,
        OrderEvent $orderEvent,
        NotificationChannel $channel,
        RecipientInterface $recipient,
    ): self {
        return new self($orderId, $orderEvent, $channel, $recipient);
    }

    /**
     * @throws InvalidTaskStateException - если задача не в статусе Pending
     */
    public function startFetching(): void
    {
        if ($this->status !== TaskStatus::Pending) {
            throw new InvalidTaskStateException(sprintf(
                "Задача № %d: начать получение данных можно только из статуса '%s' (сейчас '%s').",
                $this->id->value(),
                TaskStatus::Pending->value,
                $this->status->value,
            ));
        }

        $this->status = TaskStatus::Fetching;

        $this->recordTransition();
    }

    /**
     * @throws InvalidTaskStateException - если задача не в статусе Fetching или нет данных заказа
     */
    public function startSending(): void
    {
        if ($this->status !== TaskStatus::Fetching) {
            throw new InvalidTaskStateException(sprintf(
                "Задача № %d: начать отправку уведомления можно только из статуса '%s' (сейчас '%s').",
                $this->id->value(),
                TaskStatus::Fetching->value,
                $this->status->value,
            ));
        }

        if ($this->orderData === null) {
            throw new InvalidTaskStateException(sprintf(
                'Задача № %d: нельзя начать отправку без данных заказа.',
                $this->id->value(),
            ));
        }

        $this->status = TaskStatus::Sending;

        $this->recordTransition();
    }

    /**
     * @throws InvalidTaskStateException - если задача уже завершена или не в статусе Sending
     */
    public function complete(): void
    {
        if ($this->status === TaskStatus::Completed) {
            throw new InvalidTaskStateException(sprintf('Задача № %d уже завершена.', $this->id->value()));
        }

        if ($this->status !== TaskStatus::Sending) {
            throw new InvalidTaskStateException(sprintf(
                "Задача № %d: завершить обработку можно только из статуса '%s' (сейчас '%s').",
                $this->id->value(),
                TaskStatus::Sending->value,
                $this->status->value,
            ));
        }

        $this->status = TaskStatus::Completed;
        $this->processedAt = new \DateTimeImmutable();

        $this->recordTransition();
    }

    /**
     * @throws InvalidTaskStateException - если задача уже в финальном статусе
     */
    public function fail(string $reason): void
    {
        if ($this->hasFinalStatus()) {
            throw new InvalidTaskStateException(sprintf(
                "Задача № %d уже в финальном статусе '%s' - ошибку зафиксировать нельзя.",
                $this->id->value(),
                $this->status->value,
            ));
        }

        $this->status = TaskStatus::Failed;
        $this->failureReason = $reason;
        $this->processedAt = new \DateTimeImmutable();

        $this->recordTransition();
    }

    /**
     * Начало очередной попытки обработки.
     *
     * @throws InvalidTaskStateException - если задача в финальном статусе
     * @throws MaxAttemptsExceededException - если лимит попыток исчерпан
     */
    public function beginAttempt(int $maxAttempts): void
    {
        if ($this->hasFinalStatus()) {
            throw new InvalidTaskStateException(sprintf(
                "Задача № %d уже в финальном статусе '%s' и не может обрабатываться.",
                $this->id->value(),
                $this->status->value,
            ));
        }

        if (!$this->canAttempt($maxAttempts)) {
            throw new MaxAttemptsExceededException(sprintf(
                'Задача № %d исчерпала лимит попыток (%d).',
                $this->id->value(),
                $maxAttempts,
            ));
        }

        $this->attempts++;
    }

    /**
     * @throws InvalidTaskStateException - если задача в финальном статусе или ещё не начинала обработку
     */
    public function restart(): void
    {
        if ($this->hasFinalStatus()) {
            throw new InvalidTaskStateException(sprintf(
                "Задача № %d в финальном статусе '%s' и не может быть возобновлена.",
                $this->id->value(),
                $this->status->value,
            ));
        }

        if ($this->status === TaskStatus::Pending) {
            throw new InvalidTaskStateException(sprintf(
                'Задача № %d ещё не начинала обработку - возобновлять нечего.',
                $this->id->value(),
            ));
        }

        if ($this->status === TaskStatus::Fetching) {
            return; // осиротевшая задача уже в Fetching - переход в саму себя не фиксируем
        }

        $this->status = TaskStatus::Fetching;
        $this->orderData = null;

        $this->recordTransition();
    }

    /**
     * Задача пережила неудачную попытку и ждёт повторной доставки из ретрай-очереди.
     *
     * @throws InvalidTaskStateException - если задача в финальном статусе или попытка ещё не начиналась
     */
    public function retry(): void
    {
        if ($this->hasFinalStatus()) {
            throw new InvalidTaskStateException(sprintf(
                "Задача № %d в финальном статусе '%s' - переводить в '%s' нельзя.",
                $this->id->value(),
                $this->status->value,
                TaskStatus::Retrying->value,
            ));
        }

        if ($this->status === TaskStatus::Pending) {
            throw new InvalidTaskStateException(sprintf(
                'Задача № %d ещё не начинала обработку - retry недопустим.',
                $this->id->value(),
            ));
        }

        $this->status = TaskStatus::Retrying;

        $this->recordTransition();
    }

    /**
     * Прямые переходы жизненного цикла. Сброс попытки (restart) выполняется
     * отдельным методом - это не переход в статус, а откат прогресса.
     *
     * @throws \LogicException - при переходе в Pending или в Failed без причины
     * @throws InvalidTaskStateException - если переход недопустим из текущего статуса
     */
    public function transitionTo(TaskStatus $status, ?string $reason = null): void
    {
        match ($status) {
            TaskStatus::Pending => throw new \LogicException(sprintf(
                "Статус '%s' устанавливается только при создании задачи.",
                TaskStatus::Pending->value,
            )),

            TaskStatus::Fetching => $this->startFetching(),
            TaskStatus::Sending => $this->startSending(),

            TaskStatus::Retrying => $this->retry(),

            TaskStatus::Completed => $this->complete(),
            TaskStatus::Failed => $this->fail($reason ?? throw new \LogicException(
                sprintf("Для перехода в статус '%s' требуется причина.", TaskStatus::Failed->value),
            )),
        };
    }

    /**
     * @throws InvalidTaskStateException - если задача не в статусе Fetching или данные уже приложены
     */
    public function attachOrderData(OrderData $orderData): void
    {
        if ($this->status !== TaskStatus::Fetching) {
            throw new InvalidTaskStateException(sprintf(
                "Задача № %d: данные по заказу можно приложить только в статусе '%s' (сейчас '%s').",
                $this->id->value(),
                TaskStatus::Fetching->value,
                $this->status->value,
            ));
        }

        if ($this->orderData !== null) {
            throw new InvalidTaskStateException(sprintf(
                'Задача № %d: данные по заказу уже приложены.',
                $this->id->value(),
            ));
        }

        $this->orderData = $orderData;
    }

    /**
     * @throws InvalidTaskStateException - если данные заказа не приложены
     */
    public function requireOrderData(): OrderData
    {
        if ($this->orderData === null) {
            throw new InvalidTaskStateException(sprintf(
                'Задача № %d: данные по заказу не приложены.',
                $this->id->value(),
            ));
        }

        return $this->orderData;
    }

    public function hasFinalStatus(): bool
    {
        return $this->status->isFinal();
    }

    private function canAttempt(int $maxAttempts): bool
    {
        return $this->attempts < $maxAttempts;
    }

    private function recordTransition(): void
    {
        $this->statusHistory[] = $this->status;
    }
}
