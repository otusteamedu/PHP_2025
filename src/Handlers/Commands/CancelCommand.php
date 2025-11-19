<?php

namespace App\Handlers\Commands;

use App\Repositories\UserStateRepository;
use App\Services\TelegramService;
use App\Models\UserState;

class CancelCommand extends BaseCommand
{
    private UserStateRepository $userStateRepository;

    public function __construct(
        TelegramService $telegramService,
        UserStateRepository $userStateRepository
    ) {
        parent::__construct($telegramService);
        $this->userStateRepository = $userStateRepository;
    }

    public function execute(int $chatId, int $userId, string $args = ''): void
    {
        $userState = $this->userStateRepository->findByUserId($userId);
        
        if ($userState && $userState->getState() !== UserState::STATE_NONE) {
            $this->userStateRepository->delete($userId);
            $this->telegramService->sendMessage($chatId, "❌ Создание задачи отменено.");
        } else {
            $this->telegramService->sendMessage($chatId, "🤔 Нечего отменять. Используйте /new_task чтобы начать создание задачи.");
        }
    }
}