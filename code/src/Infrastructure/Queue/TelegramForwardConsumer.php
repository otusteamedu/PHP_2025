<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Queue;

use MkdBot\Application\DTO\ForwardMessageDTO;
use MkdBot\Application\UseCase\ForwardToTelegram;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Domain\Interface\FallbackMessageRepositoryInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

/**
 * Consumer дублирования Max->Telegram — обрабатывает сообщения из mkd.telegram.forward
 * Вызывает Use Case ForwardToTelegram (не напрямую API-клиенты)
 */
class TelegramForwardConsumer extends RabbitMQConsumer
{
    public function __construct(
        string $host,
        int $port,
        string $login,
        string $password,
        string $vhost,
        FallbackMessageRepositoryInterface $fallbackRepo,
        LoggerInterface $logger,
        private readonly ForwardToTelegram $forwardToTelegram,
        private readonly ?\MaxMessenger\Bot\MaxApiClient $maxApiClient = null, // опционально для fallback
        ?DatabaseConnectionInterface $dbConnection = null,
    ) {
        parent::__construct($host, $port, $login, $password, $vhost, 'mkd.telegram.forward', $fallbackRepo, $logger, $dbConnection);
    }

    protected function processMessage(string $body, array $headers): void
    {
        $data = json_decode($body, true);
        if ($data === null) {
            throw new RuntimeException("Не удалось декодировать JSON сообщения: " . $body);
        }

        // Резерв: если документы без URL — получаем через getMessageById
        $attachments = $data['attachments'] ?? [];
        $mid = $data['source_message_mid'] ?? '';

        foreach ($attachments as &$attachment) {
            if (($attachment['type'] === 'document') && empty($attachment['url']) && $this->maxApiClient !== null && $mid !== '') {
                $this->logger->info("Попытка получить URL документа через getMessageById: mid={$mid}");
                try {
                    $message = $this->maxApiClient->getMessageById($mid);
                    $msgAttachments = $message->getBody()->getAttachments() ?? [];
                    foreach ($msgAttachments as $msgAttachment) {
                        if ($msgAttachment->getTypeRaw() === 'file') {
                            if ($msgAttachment instanceof \MaxMessenger\Bot\Models\Responses\FileAttachment) {
                                $payload = $msgAttachment->getPayload();
                                if ($payload->offsetExists('url')) {
                                    $attachment['url'] = $payload->offsetGet('url');
                                    $this->logger->info("URL документа получен из getMessageById");
                                }
                                // filename — поле FileAttachment, а не FileAttachmentPayload
                                if (empty($attachment['filename'])) {
                                    $attachment['filename'] = $msgAttachment->getFilename();
                                }
                                if ($payload->offsetExists('url')) {
                                    break;
                                }
                            }
                        }
                    }
                } catch (Throwable $e) {
                    $this->logger->warning("Не удалось получить URL документа через getMessageById: " . $e->getMessage());
                }
            }
        }
        unset($attachment);

        $data['attachments'] = $attachments;

        $dto = new ForwardMessageDTO(
            text: $data['text'] ?? '',
            attachments: $data['attachments'] ?? [],
            sourceMessageMid: $data['source_message_mid'] ?? '',
            chatId: $data['chat_id'] ?? 0,
            chatType: $data['chat_type'] ?? '',
            messageUrl: $data['message_url'] ?? null,
        );

        $this->logger->info("Дублирование сообщения: mid={$dto->sourceMessageMid}, chatType={$dto->chatType}");

        $this->forwardToTelegram->execute($dto);
    }
}
