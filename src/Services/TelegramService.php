<?php

namespace App\Services;

use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;
use App\Core\Config;

class TelegramService
{
    private Api $telegram;

    public function __construct()
    {
        $this->telegram = new Api(Config::get('telegram.bot_token'));
    }

    public function getTelegram(): Api
    {
        return $this->telegram;
    }

    public function sendMessage(int $chatId, string $text, array $options = []): void
    {
        try {
            $this->telegram->sendMessage(array_merge([
                'chat_id' => $chatId,
                'text' => $text,
            ], $options));
        } catch (\Exception $e) {
            error_log("Error sending message: " . $e->getMessage());
        }
    }

    public function sendKeyboard(int $chatId, string $text, array $buttons, bool $inline = true): void
    {
        try {
            if ($inline) {
                $keyboard = Keyboard::make([
                    'inline_keyboard' => $buttons,
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ]);
            } else {
                $keyboard = Keyboard::make([
                    'keyboard' => $buttons,
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ]);
            }

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'reply_markup' => $keyboard
            ]);
        } catch (\Exception $e) {
            error_log("Error sending keyboard: " . $e->getMessage());
        }
    }

    public function removeKeyboard(int $chatId, string $text): void
    {
        try {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'reply_markup' => Keyboard::remove()
            ]);
        } catch (\Exception $e) {
            error_log("Error removing keyboard: " . $e->getMessage());
        }
    }
}