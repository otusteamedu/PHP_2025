<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Application\Interface\TelegramNotificationServiceInterface;
use App\Domain\ValueObject\TelegramChatId;
use App\Infrastructure\Logging\Logger;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

final class TelegramNotificationService implements TelegramNotificationServiceInterface
{
    private Client $httpClient;

    public function __construct(
        private string $botToken
    ) {
        $this->httpClient = new Client([
            'base_uri' => 'https://api.telegram.org/bot' . $botToken . '/',
            'timeout' => 10
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function sendMessage(TelegramChatId $chatId, string $message): void
    {
        try {
            $response = $this->httpClient->post('sendMessage', [
                'form_params' => [
                    'chat_id' => $chatId->toInt(),
                    'text' => $message,
                    'parse_mode' => 'HTML'
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            if (!$result['ok']) {
                throw new RuntimeException('Telegram API error: ' . ($result['description'] ?? 'Unknown error'));
            }

            $logger = Logger::getInstance();
            $logger->info('Telegram message sent successfully', ['chat_id' => $chatId->value()]);
        } catch (GuzzleException $e) {
            throw new RuntimeException('Failed to send Telegram message: ' . $e->getMessage());
        }
    }
}
