<?php

declare(strict_types=1);

namespace MkdBot\Presentation\Mapper;

use MaxMessenger\Bot\Models\Responses\BotStartedUpdate;
use MaxMessenger\Bot\Models\Responses\BotStoppedUpdate;
use MaxMessenger\Bot\Models\Responses\MessageCallbackUpdate;
use MaxMessenger\Bot\Models\Responses\MessageCreatedUpdate;
use MaxMessenger\Bot\Models\Responses\Update;
use MkdBot\Application\DTO\MaxBotEventDTO;
use MkdBot\Application\DTO\MaxCallbackDTO;
use MkdBot\Application\DTO\MaxMessageDTO;
use Psr\Log\LoggerInterface;
use TypeError;

/**
 * Маппер Update -> Domain DTO
 * Преобразует библиотечные объекты Max API в DTO Application-слоя
 * Библиотечные типы НЕ проникают за пределы Presentation-слоя
 */
class MaxUpdateMapper
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Преобразует Update в Domain DTO или null (для неизвестных типов)
     */
    public function map(Update $update): MaxMessageDTO|MaxCallbackDTO|MaxBotEventDTO|null
    {
        return match (true) {
            $update instanceof MessageCreatedUpdate => $this->mapMessageCreated($update),
            $update instanceof MessageCallbackUpdate => $this->mapMessageCallback($update),
            $update instanceof BotStartedUpdate => $this->mapBotStarted($update),
            $update instanceof BotStoppedUpdate => $this->mapBotStopped($update),
            default => $this->handleUnknownUpdate($update),
        };
    }

    /**
     * Маппинг MessageCreatedUpdate -> MaxMessageDTO
     */
    private function mapMessageCreated(MessageCreatedUpdate $update): MaxMessageDTO
    {
        $message = $update->getMessage();
        $body = $message->getBody();
        $sender = $message->getSender();
        $recipient = $message->getRecipient();

        // Извлекаем вложения (фото и документы) из MessageBody
        $attachments = $this->extractAttachments($body->getAttachments() ?? []);

        $chatType = $recipient->getChatType()->value ?? '';
        $mid = $body->getMid();

        // Диагностическое логирование для отладки маршрутизации канальных сообщений
        $this->logger->debug("MaxUpdateMapper: message_created mid={$mid}, chatType={$chatType}, chatId={$recipient->getChatId()}, userId=" . ($sender?->getUserId() ?? 'null') . ", attachments_count=" . count($attachments));

        return new MaxMessageDTO(
            userId: $sender?->getUserId(),
            userName: $sender?->getFullName(),
            text: $body->getText(),
            mid: $mid,
            chatId: $recipient->getChatId(),
            chatType: $chatType,
            attachments: $attachments,
            messageUrl: $message->getUrl(),
        );
    }

    /**
     * Маппинг MessageCallbackUpdate -> MaxCallbackDTO
     * Callback::getPayload() возвращает string — маппер вызывает json_decode()
     */
    private function mapMessageCallback(MessageCallbackUpdate $update): MaxCallbackDTO
    {
        $callback = $update->getCallback();
        $message = $update->getMessage();

        // Парсим payload из JSON строки в array
        $payloadStr = $callback->getPayload();
        $payload = json_decode($payloadStr, true) ?? [];

        // chatId из сообщения (MessageCallbackUpdate не содержит chat_id напрямую)
        $chatId = $message->getRecipient()->getChatId();

        // getFullName() может выбросить TypeError если first_name отсутствует — обрабатываем безопасно
        $userName = null;
        try {
            $userName = $callback->getUser()->getFullName();
        } catch (TypeError) {
            // first_name отсутствует — userName останется null
        }

        // callbackId может быть пустым в Max API — передаём null, answerCallback не вызовется
        $callbackId = $callback->getCallbackId();
        if (empty($callbackId)) {
            $callbackId = null;
            $this->logger->warning("callbackId пустой в Max API, передан null вместо answerCallback");
        }

        return new MaxCallbackDTO(
            callbackId: $callbackId,
            payload: $payload,
            userId: $callback->getUser()->getUserId(),
            chatId: $chatId,
            userName: $userName,
        );
    }

    /**
     * Маппинг BotStartedUpdate -> MaxBotEventDTO
     * Без chatType — bot_started всегда в диалоге
     */
    private function mapBotStarted(BotStartedUpdate $update): MaxBotEventDTO
    {
        return new MaxBotEventDTO(
            chatId: $update->getChatId(),
            userId: $update->getUser()->getUserId(),
            userName: $update->getUser()->getFullName(),
            eventType: 'bot_started',
            payload: $update->getPayload(),
        );
    }

    /**
     * Маппинг BotStoppedUpdate -> MaxBotEventDTO
     * Без chatType - bot_stopped всегда в диалоге
     */
    private function mapBotStopped(BotStoppedUpdate $update): MaxBotEventDTO
    {
        return new MaxBotEventDTO(
            chatId: $update->getChatId(),
            userId: $update->getUser()->getUserId(),
            userName: $update->getUser()->getFullName(),
            eventType: 'bot_stopped',
        );
    }

    /**
     * Обработка неизвестных/неподдерживаемых update-типов
     * Возвращает null + логирует WARNING
     */
    private function handleUnknownUpdate(Update $update): null
    {
        $updateType = $update->getUpdateTypeRaw();
        $this->logger->warning("Неподдерживаемый тип обновления: {$updateType}");

        return null;
    }

    /**
     * Извлекает вложения из сообщения Max
     * Фото: безопасно через getUrl() (PhotoAttachmentPayload наследует BaseResponseModel)
     * Документы: НЕБЕЗОПАСНО вызывать getUrl() на FileAttachmentPayload!
     * Только через offsetExists('url') + offsetGet('url') или getRawData()
     *
     * @param array $attachments Массив вложений из Message::getAttachments()
     * @return array<array{type: string, url: string|null, token: string|null, filename: string|null, size: int|null}>
     */
    private function extractAttachments(array $attachments): array
    {
        $result = [];

        foreach ($attachments as $attachment) {
            // getTypeRaw() для строкового сравнения: Max API — 'image' (не 'photo'), 'file'
            $type = $attachment->getTypeRaw() ?? '';

            if ($type === 'image') {
                $payload = $attachment->getPayload();
                $result[] = [
                    'type' => 'photo',
                    'url' => $payload?->getUrl() ?? null,
                    'token' => null,
                    'filename' => null,
                    'size' => null,
                ];
            } elseif ($type === 'file') {
                // filename/size — на уровне FileAttachment (не payload), url — через ArrayAccess payload
                $payload = $attachment->getPayload();
                $url = null;
                $token = null;
                $filename = null;
                $size = null;

                // filename и size — поля FileAttachment, а не FileAttachmentPayload
                // getFilename()/getSize() могут выбросить TypeError при неполных данных
                if ($attachment instanceof \MaxMessenger\Bot\Models\Responses\FileAttachment) {
                    try {
                        $filename = $attachment->getFilename();
                    } catch (TypeError) {
                        $filename = null;
                    }
                    try {
                        $size = $attachment->getSize();
                    } catch (TypeError) {
                        $size = null;
                    }
                }

                if ($payload !== null) {
                    if ($payload->offsetExists('url')) {
                        $url = $payload->offsetGet('url');
                    }
                    if ($payload->offsetExists('token')) {
                        $token = $payload->offsetGet('token');
                    }
                }

                $result[] = [
                    'type' => 'document',
                    'url' => $url,
                    'token' => $token,
                    'filename' => $filename,
                    'size' => $size,
                ];
            }
        }

        return $result;
    }
}
