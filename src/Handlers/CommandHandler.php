<?php

namespace App\Handlers;

use App\Services\TelegramService;
use App\Services\TaskService;
use App\Services\ReminderService;
use App\Services\AnalyticsService;
use App\Repositories\UserStateRepository;
use App\Models\UserState;
use App\Handlers\Commands\StartCommand;
use App\Handlers\Commands\NewTaskCommand;
use App\Handlers\Commands\ListTasksCommand;
use App\Handlers\Commands\DeleteTaskCommand;
use App\Handlers\Commands\CompleteTaskCommand;
use App\Handlers\Commands\IncompleteTaskCommand;
use App\Handlers\Commands\SetReminderCommand;
use App\Handlers\Commands\RemoveReminderCommand;
use App\Handlers\Commands\StatsCommand;
use App\Handlers\Commands\CancelCommand;
use App\Handlers\Commands\UnknownCommand;
use App\Handlers\States\StateHandler;
use App\Handlers\CallbackQueryHandler;

class CommandHandler
{
    private TelegramService $telegramService;
    private TaskService $taskService;
    private ReminderService $reminderService;
    private UserStateRepository $userStateRepository;
    private AnalyticsService $analyticsService;
    
    private array $commands;
    private StateHandler $stateHandler;
    private CallbackQueryHandler $callbackQueryHandler;

    public function __construct(
        TelegramService $telegramService, 
        TaskService $taskService,
        ReminderService $reminderService,
        UserStateRepository $userStateRepository,
        AnalyticsService $analyticsService
    ) {
        $this->telegramService = $telegramService;
        $this->taskService = $taskService;
        $this->reminderService = $reminderService;
        $this->userStateRepository = $userStateRepository;
        $this->analyticsService = $analyticsService;
        
        $this->initializeHandlers();
    }

    private function initializeHandlers(): void
    {
        // Инициализация обработчиков команд
        $this->commands = [
            'start' => new StartCommand($this->telegramService),
            'new_task' => new NewTaskCommand($this->telegramService, $this->userStateRepository),
            'list_tasks' => new ListTasksCommand($this->telegramService, $this->taskService),
            'delete_task' => new DeleteTaskCommand($this->telegramService, $this->taskService),
            'complete_task' => new CompleteTaskCommand($this->telegramService, $this->taskService),
            'incomplete_task' => new IncompleteTaskCommand($this->telegramService, $this->taskService),
            'set_reminder' => new SetReminderCommand($this->telegramService, $this->taskService, $this->userStateRepository),
            'remove_reminder' => new RemoveReminderCommand($this->telegramService, $this->taskService, $this->reminderService),
            'stats' => new StatsCommand($this->telegramService, $this->analyticsService),
            'cancel' => new CancelCommand($this->telegramService, $this->userStateRepository),
            'unknown' => new UnknownCommand($this->telegramService)
        ];

        $this->stateHandler = new StateHandler(
            $this->telegramService,
            $this->taskService,
            $this->reminderService,
            $this->userStateRepository
        );

        $this->callbackQueryHandler = new CallbackQueryHandler(
            $this->telegramService,
            $this->taskService,
            $this->reminderService,
            $this->userStateRepository
        );
    }

    public function handle($update): void
    {
        $message = $update->getMessage();
        $callbackQuery = $update->getCallbackQuery();
        
        if ($callbackQuery) {
            $this->callbackQueryHandler->handle($callbackQuery);
            return;
        }

        if (!$message) {
            return;
        }

        $messageData = $message->toArray();
        $text = $messageData['text'] ?? null;
        $chatId = $messageData['chat']['id'] ?? null;
        $userId = $messageData['from']['id'] ?? null;

        if (!$text || !$chatId || !$userId) {
            return;
        }

        // проверяем команду /cancel независимо от состояния
        if ($this->isCancelCommand($text)) {
            $this->commands['cancel']->execute($chatId, $userId);
            return;
        }

        // Получаем состояние пользователя
        $userState = $this->userStateRepository->findByUserId($userId) ?? new UserState($userId);

        // Если пользователь в процессе создания задачи, обрабатываем состояние
        if ($userState->getState() !== UserState::STATE_NONE) {
            $this->stateHandler->handle($userState, $text, $chatId, $userId);
            return;
        }

        // Обрабатываем обычные команды
        $command = $this->parseCommand($text);
        $commandName = $command['type'];
        
        if (isset($this->commands[$commandName])) {
            $this->commands[$commandName]->execute($chatId, $userId, $command['args']);
        } else {
            $this->commands['unknown']->execute($chatId, $userId);
        }
    }

    private function isCancelCommand(string $text): bool
    {
        $command = $this->parseCommand($text);
        return $command['type'] === 'cancel';
    }

    private function parseCommand(string $text): array
    {
        if (str_starts_with($text, '/')) {
            $parts = explode(' ', $text, 2);
            $command = substr($parts[0], 1);
            $args = $parts[1] ?? '';
            
            return ['type' => $command, 'args' => $args];
        }

        return ['type' => 'text', 'args' => $text];
    }
}