<?php

declare(strict_types=1);

namespace MkdBot\Application\Service;

use MkdBot\Domain\Interface\MaxBotClientInterface;

/**
 * Отправка главного меню с inline-клавиатурой
 * Единая точка для формирования и отправки меню — устраняет дублирование
 */
class MainMenuSender
{
    public function __construct(
        private readonly MaxBotClientInterface $maxBot,
    ) {
    }

    public function send(int $userId): void
    {
        $this->maxBot->sendMessageWithInlineKeyboard(
            $userId,
            "🏠 Я — МКД-Бот, хранитель канала!\nВыберите действие:",
            [
                ['text' => 'УК', 'payload' => ['action' => 'contacts', 'type' => 'uk'], 'intent' => 'default', 'row' => 0],
                ['text' => 'Совет дома', 'payload' => ['action' => 'contacts', 'type' => 'council'], 'intent' => 'default', 'row' => 0],
                ['text' => 'Предложение совету дома', 'payload' => ['action' => 'suggestion', 'step' => 'menu'], 'intent' => 'default', 'row' => 1],
                ['text' => 'Улучшение бота', 'payload' => ['action' => 'feature', 'step' => 'menu'], 'intent' => 'default', 'row' => 2],
                ['text' => 'Вопрос ИИ', 'payload' => ['action' => 'rag_query', 'step' => 'menu'], 'intent' => 'default', 'row' => 3],
            ],
        );
    }
}
