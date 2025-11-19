<?php

namespace App\Handlers\Commands;

use App\Services\TelegramService;
use App\Repositories\UserStateRepository;
use App\Models\UserState;

class NewTaskCommand extends BaseCommand
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
        try {
            $userState = new UserState($userId, UserState::STATE_AWAITING_TASK_TITLE);
            $success = $this->userStateRepository->save($userState);

            if (!$success) {
                throw new \Exception("Failed to save user state");
            }

            $message = "📝 Давайте создадим новую задачу!\n\n";
            $message .= "Введите название задачи:\n\n";
            $message .= "💡 Используйте /cancel чтобы отменить создание";
            
            $this->telegramService->sendMessage($chatId, $message);
            
        } catch (\Exception $e) {
            error_log("Error starting new task: " . $e->getMessage());
            $this->telegramService->sendMessage($chatId, "❌ Произошла ошибка при создании задачи. Попробуйте еще раз.");
        }
    }
}