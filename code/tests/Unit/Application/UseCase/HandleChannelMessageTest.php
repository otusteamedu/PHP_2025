<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Application\UseCase;

use MkdBot\Application\DTO\MaxMessageDTO;
use MkdBot\Application\UseCase\HandleChannelMessage;
use MkdBot\Domain\Enum\QueueNameType;
use MkdBot\Domain\Interface\QueuePublisherInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class HandleChannelMessageTest extends TestCase
{
    public function testChannelMessagePublishesToQueue(): void
    {
        $queuePublisher = $this->createMock(QueuePublisherInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $queuePublisher->expects($this->once())
            ->method('publish')
            ->with(QueueNameType::TelegramForward, $this->isType('array'));

        $useCase = new HandleChannelMessage($queuePublisher, $logger);

        $dto = new MaxMessageDTO(
            userId: null,
            userName: null,
            text: 'Channel post',
            mid: 'mid.1',
            chatId: 123,
            chatType: 'channel',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Канальное сообщение (chatType = 'channel') с вложениями — пересылается в Telegram
     */
    public function testChannelMessageWithAttachmentsForwardsToTelegram(): void
    {
        $queuePublisher = $this->createMock(QueuePublisherInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $queuePublisher->expects($this->once())
            ->method('publish')
            ->with(QueueNameType::TelegramForward, $this->callback(function (array $data) {
                return $data['text'] === 'Channel with photo'
                    && count($data['attachments']) === 1
                    && $data['chat_type'] === 'channel';
            }));

        $useCase = new HandleChannelMessage($queuePublisher, $logger);

        $dto = new MaxMessageDTO(
            userId: null,
            userName: null,
            text: 'Channel with photo',
            mid: 'mid.chphoto',
            chatId: 999,
            chatType: 'channel',
            attachments: [['type' => 'photo', 'url' => 'https://max.ru/p.jpg', 'token' => null, 'filename' => null, 'size' => null]],
            messageUrl: 'https://max.ru/post/42',
        );

        $useCase->execute($dto);
    }
}
