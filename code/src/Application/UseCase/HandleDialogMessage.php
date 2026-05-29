<?php

declare(strict_types=1);

namespace MkdBot\Application\UseCase;

use MkdBot\Application\DTO\MaxMessageDTO;
use MkdBot\Application\Service\MainMenuSender;
use MkdBot\Domain\Enum\ConversationStep;
use MkdBot\Domain\Interface\ConversationStateRepositoryInterface;
use MkdBot\Domain\Interface\MaxBotClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Обработка текстовых сообщений — ввод темы/описания в многошаговом диалоге
 */
class HandleDialogMessage
{
    private const SUBJECT_MAX_LENGTH = 200;
    private const DESCRIPTION_MAX_LENGTH = 3000;

    public function __construct(
        private readonly ConversationStateRepositoryInterface $stateRepo,
        private readonly MaxBotClientInterface $maxBot,
        private readonly LoggerInterface $logger,
        private readonly MainMenuSender $mainMenuSender,
    ) {
    }

    /**
     * Обрабатывает входящее текстовое сообщение из диалога
     */
    public function execute(MaxMessageDTO $dto): void
    {
        $this->logger->debug("Маршрутизация: сообщение из диалога, chatType={$dto->chatType}, userId={$dto->userId}");

        // Команды /start и /help — показ главного меню
        if (in_array($dto->text, ['/start', '/help'], true)) {
            $this->stateRepo->deleteByUserId($dto->userId);
            $this->mainMenuSender->send($dto->userId);
            return;
        }

        // Если нет активной сессии — показать главное меню
        $state = $this->stateRepo->findByUserId($dto->userId);
        if ($state === null || $state->isExpired()) {
            if ($state?->isExpired()) {
                $this->maxBot->sendMessageToUser($dto->userId, '⏰ Сессия истекла, начните заново');
                $this->stateRepo->deleteByUserId($dto->userId);
            }
            $this->mainMenuSender->send($dto->userId);
            return;
        }

        // Обработка нетекстового ввода на шагах диалога
        if ($state->getCurrentStep() === ConversationStep::AwaitingSubject
            || $state->getCurrentStep() === ConversationStep::AwaitingDescription
            || $state->getCurrentStep() === ConversationStep::AwaitingQuestion
        ) {
            if ($this->isEmptyMessage($dto)) {
                $this->maxBot->sendMessageToUser($dto->userId, 'Пожалуйста, введите текст');
                return;
            }
            // Предупреждение если есть вложения — они игнорируются
            if ($this->hasAttachments($dto)) {
                $this->maxBot->sendMessageToUser($dto->userId, '📎 Вложения не поддерживаются, принят только текст');
            }
        }

        // Обработка по текущему шагу диалога
        match ($state->getCurrentStep()) {
            ConversationStep::AwaitingSubject => $this->handleSubjectInput($dto, $state),
            ConversationStep::AwaitingDescription => $this->handleDescriptionInput($dto, $state),
            ConversationStep::AwaitingQuestion => $this->handleQuestionInput($dto, $state),
            default => $this->mainMenuSender->send($dto->userId),
        };
    }

    /**
     * Обрабатывает ввод темы предложения
     */
    private function handleSubjectInput(MaxMessageDTO $dto, \MkdBot\Domain\Entity\ConversationState $state): void
    {
        if (mb_strlen($dto->text) > self::SUBJECT_MAX_LENGTH) {
            $this->maxBot->sendMessageToUser(
                $dto->userId,
                "❌ Тема слишком длинная, максимум " . self::SUBJECT_MAX_LENGTH . " символов. Пожалуйста, введите тему заново:",
            );
            return;
        }

        // Сохраняем тему и имя пользователя для handleConfirm
        $state->setDatum('subject', $dto->text);
        if ($dto->userName !== null) {
            $state->setDatum('user_name', $dto->userName);
        }
        $state->setStep(ConversationStep::AwaitingDescription);
        $this->stateRepo->save($state);

        $this->maxBot->sendMessageWithInlineKeyboard(
            $dto->userId,
            "📝 Введите описание:",
            [['text' => '❌ Отмена', 'payload' => ['action' => 'cancel', 'step' => 'awaiting_description'], 'intent' => 'negative']],
        );
    }

    /**
     * Обрабатывает ввод описания предложения
     */
    private function handleDescriptionInput(MaxMessageDTO $dto, \MkdBot\Domain\Entity\ConversationState $state): void
    {
        if (mb_strlen($dto->text) > self::DESCRIPTION_MAX_LENGTH) {
            $this->maxBot->sendMessageToUser(
                $dto->userId,
                "❌ Описание слишком длинное, максимум " . self::DESCRIPTION_MAX_LENGTH . " символов. Пожалуйста, введите описание заново:",
            );
            return;
        }

        $state->setDatum('content', $dto->text);
        $state->setStep(ConversationStep::Preview);
        $this->stateRepo->save($state);

        $subject = $state->getData()['subject'] ?? '';
        $content = $state->getData()['content'] ?? '';
        $type = $state->getData()['type'] ?? 'feature';

        $previewText = "📋 Превью предложения:\n"
            . "📌 Тема: {$subject}\n"
            . "📝 Описание: {$content}";

        $this->maxBot->sendMessageWithInlineKeyboard(
            $dto->userId,
            $previewText,
            [
                ['text' => '✅ Подтвердить', 'payload' => ['action' => 'confirm', 'type' => $type, 'step' => 'preview'], 'intent' => 'positive'],
                ['text' => '❌ Отменить', 'payload' => ['action' => 'cancel', 'step' => 'preview'], 'intent' => 'negative'],
            ],
        );
    }

    /**
     * Обрабатывает ввод вопроса для RAG (v1 — заглушка)
     */
    private function handleQuestionInput(MaxMessageDTO $dto, \MkdBot\Domain\Entity\ConversationState $state): void
    {
        $this->maxBot->sendMessageToUser($dto->userId, '🔧 Функция в разработке');
        $state->reset();
        $this->stateRepo->save($state);
        $this->mainMenuSender->send($dto->userId);
    }

    /**
     * Проверяет, что сообщение пустое (нет текста)
     */
    private function isEmptyMessage(MaxMessageDTO $dto): bool
    {
        return trim($dto->text) === '';
    }

    /**
     * Проверяет, есть ли в сообщении вложения
     */
    private function hasAttachments(MaxMessageDTO $dto): bool
    {
        return !empty($dto->attachments);
    }
}
