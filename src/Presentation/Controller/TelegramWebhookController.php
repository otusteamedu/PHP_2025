<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Infrastructure\Config\AppConfig;
use Exception;

final class TelegramWebhookController
{
    private string $botToken;

    public function __construct()
    {
        AppConfig::load();
        $this->botToken = AppConfig::getTelegramBotToken();
    }

    /**
     * Обрабатывает входящий webhook от Telegram
     */
    public function handleWebhook(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);

        if (!$input) {
            http_response_code(400);
            return;
        }

        $this->processUpdate($input);
    }

    /**
     * Устанавливает webhook URL для Telegram бота
     */
    public function setWebhook(): void
    {
        $httpsUrl = $this->getNgrokHttpsUrl();

        if (!$httpsUrl) {
            http_response_code(500);
            echo json_encode(['error' => 'HTTPS URL not available'], JSON_THROW_ON_ERROR);
            return;
        }

        $webhookUrl = $httpsUrl . '/api/telegram/webhook';
        $url = "https://api.telegram.org/bot{$this->botToken}/setWebhook";

        $data = [
            'url' => $webhookUrl,
            'allowed_updates' => ['message']
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_THROW_ON_ERROR));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        http_response_code($httpCode);
        echo $response;
    }

    /**
     * Обрабатывает обновление от Telegram API
     */
    public function processUpdate(array $update): void
    {
        if (!isset($update['message'])) {
            return;
        }

        $message = $update['message'];
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $firstName = $message['from']['first_name'] ?? '';

        match ($text) {
            '/start' => $this->handleStart($chatId, $firstName),
            '/form' => $this->handleForm($chatId),
            '/help' => $this->handleHelp($chatId),
            default => $this->handleUnknown($chatId)
        };
    }

    /**
     * Обрабатывает команду /start
     */
    private function handleStart(int $chatId, string $firstName): void
    {
        $message = "👋 Привет, {$firstName}!\n\n";
        $message .= "🏦 Я бот для генерации банковских выписок.\n\n";
        $message .= "📋 Доступные команды:\n";
        $message .= "/form - Открыть форму запроса выписки\n";
        $message .= "/help - Показать справку\n\n";

        $this->sendMessage($chatId, $message);
    }

    /**
     * Обрабатывает команду /form
     */
    private function handleForm(int $chatId): void
    {
        $httpsUrl = $this->getNgrokHttpsUrl();

        if (!$httpsUrl) {
            $message = "⚠️ Ошибка: HTTPS URL недоступен\n\n";
            $message .= "Убедитесь, что ngrok запущен и доступен.";
            $this->sendMessage($chatId, $message);
            return;
        }

        $webAppUrl = $httpsUrl . '/telegram?chat_id=' . $chatId . '&start_param=fullscreen';
        $message = "📋 Форма запроса банковской выписки\n\n";
        $message .= "🌐 Нажмите кнопку ниже, чтобы открыть форму:";

        $keyboard = [
            'inline_keyboard' => [
                [
                    [
                        'text' => '📋 Открыть форму',
                        'web_app' => ['url' => $webAppUrl]
                    ]
                ]
            ]
        ];

        $this->sendMessage($chatId, $message, $keyboard);
    }

    /**
     * Получает HTTPS URL от ngrok API
     */
    private function getNgrokHttpsUrl(): ?string
    {
        try {
            $ngrokApiUrl = AppConfig::getNgrokApiUrl();
            $response = @file_get_contents($ngrokApiUrl . '/api/tunnels');
            if ($response === false) {
                return null;
            }

            $tunnels = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

            if (!empty($tunnels['tunnels'])) {
                foreach ($tunnels['tunnels'] as $tunnel) {
                    if ($tunnel['proto'] === 'https') {
                        return $tunnel['public_url'];
                    }
                }
            }
        } catch (Exception $e) {
            // игнорируем ошибки
        }

        return null;
    }

    /**
     * Обрабатывает команду /help
     */
    private function handleHelp(int $chatId): void
    {
        $message = "❓ Справка по использованию бота\n\n";
        $message .= "🏦 Этот бот генерирует банковские выписки за указанный период.\n\n";
        $message .= "📋 Как использовать:\n";
        $message .= "1. Отправьте команду /form\n";
        $message .= "2. Нажмите кнопку 'Открыть форму'\n";
        $message .= "3. Заполните даты и отправьте запрос\n";
        $message .= "4. Получите выписку в Telegram\n\n";

        $this->sendMessage($chatId, $message);
    }

    /**
     * Обрабатывает неизвестные команды
     */
    private function handleUnknown(int $chatId): void
    {
        $message = "🤔 Не понимаю эту команду.\n\n";
        $message .= "📋 Доступные команды:\n";
        $message .= "/start - Запустить бота\n";
        $message .= "/form - Открыть форму запроса выписки\n";
        $message .= "/help - Показать справку";

        $this->sendMessage($chatId, $message);
    }

    /**
     * Отправляет сообщение в Telegram
     */
    private function sendMessage(int $chatId, string $text, ?array $replyMarkup = null): void
    {
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ];

        if ($replyMarkup) {
            $data['reply_markup'] = json_encode($replyMarkup, JSON_THROW_ON_ERROR);
        }

        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        curl_exec($ch);
        curl_close($ch);
    }
}
