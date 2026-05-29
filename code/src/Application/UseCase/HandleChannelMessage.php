<?php

declare(strict_types=1);

namespace MkdBot\Application\UseCase;

use MkdBot\Application\DTO\ForwardMessageDTO;
use MkdBot\Application\DTO\MaxMessageDTO;
use MkdBot\Domain\Enum\QueueNameType;
use MkdBot\Domain\Interface\QueuePublisherInterface;
use Psr\Log\LoggerInterface;

/**
 * Дублирование сообщений из канала Max в Telegram
 */
class HandleChannelMessage
{
    public function __construct(
        private readonly QueuePublisherInterface $queuePublisher,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Дублирует сообщение из канала Max в RabbitMQ для отправки в Telegram
     */
    public function execute(MaxMessageDTO $dto): void
    {
        $forwardDto = new ForwardMessageDTO(
            text: $dto->text,
            attachments: $dto->attachments,
            sourceMessageMid: $dto->mid,
            chatId: $dto->chatId,
            chatType: $dto->chatType,
            messageUrl: $dto->messageUrl,
        );

        $this->queuePublisher->publish(QueueNameType::TelegramForward, [
            'text' => $forwardDto->text,
            'attachments' => $forwardDto->attachments,
            'source_message_mid' => $forwardDto->sourceMessageMid,
            'chat_id' => $forwardDto->chatId,
            'chat_type' => $forwardDto->chatType,
            'message_url' => $forwardDto->messageUrl,
        ]);

        $this->logger->info("Сообщение из канала отправлено в очередь дублирования: mid={$dto->mid}");
    }
}
