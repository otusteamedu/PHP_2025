<?php

declare(strict_types=1);

namespace MkdBot\Application\UseCase;

use MkdBot\Application\DTO\MaxCallbackDTO;
use MkdBot\Application\DTO\ProposalDTO;
use MkdBot\Application\Service\MainMenuSender;
use MkdBot\Domain\Entity\ConversationState;
use MkdBot\Domain\Enum\ConversationStep;
use MkdBot\Domain\Enum\ProposalType;
use MkdBot\Domain\Interface\ConversationStateRepositoryInterface;
use MkdBot\Domain\Interface\MaxBotClientInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Обработка callback-кнопок (inline-клавиатура) — меню, подтверждение/отмена
 */
class HandleMessageCallback
{
    public function __construct(
        private readonly ConversationStateRepositoryInterface $stateRepo,
        private readonly MaxBotClientInterface $maxBot,
        private readonly ProcessProposal $processProposal,
        private readonly GetContacts $getContacts,
        private readonly LoggerInterface $logger,
        private readonly MainMenuSender $mainMenuSender,
    ) {
    }

    /**
     * Обрабатывает callback от inline-кнопки
     */
    public function execute(MaxCallbackDTO $dto): void
    {
        $action = $dto->payload['action'] ?? '';
        $this->logger->debug("Callback: action={$action}, userId={$dto->userId}");

        match ($action) {
            'contacts' => $this->handleContacts($dto),
            'feature', 'suggestion' => $this->handleProposalStart($dto),
            'rag_query' => $this->handleRagQuery($dto),
            'confirm' => $this->handleConfirm($dto),
            'cancel' => $this->handleCancel($dto),
            default => $this->logger->warning("Неизвестное действие callback: {$action}"),
        };
    }

    /**
     * Проверяет, соответствует ли шаг из payload текущему состоянию диалога
     * Если step не совпадает — возвращает false (callback устарелший/некорректный)
     */
    private function isStepValid(MaxCallbackDTO $dto, ?ConversationState $state): bool
    {
        $payloadStep = $dto->payload['step'] ?? null;
        if ($payloadStep === null) {
            // step не указан — разрешаем (кнопки меню, контакты)
            return true;
        }

        if ($state === null || $state->isExpired()) {
            // Сессия истекла или отсутствует — callback неактуален
            return false;
        }

        $currentStep = $state->getCurrentStep()->value;
        return $payloadStep === $currentStep;
    }

    /**
     * Отправляет answerCallbackNotification, если callbackId присутствует
     */
    private function answerCallbackIfPresent(MaxCallbackDTO $dto, string $text = '✅'): void
    {
        if ($dto->callbackId !== null && $dto->callbackId !== '') {
            $this->maxBot->answerCallbackNotification($dto->callbackId, $text);
        }
    }

    /**
     * Обработка кнопок контактов (УК / Совет дома)
     * После вывода контактов — показ главного меню
     */
    private function handleContacts(MaxCallbackDTO $dto): void
    {
        $type = $dto->payload['type'] ?? 'uk';
        $text = $this->getContacts->execute($type);
        $this->answerCallbackIfPresent($dto);
        $this->maxBot->sendMessageToUser($dto->userId, $text);
        // Показываем главное меню после контактов
        $this->mainMenuSender->send($dto->userId);
    }

    /**
     * Начало многошагового ввода предложения (Функционал / Предлож.)
     */
    private function handleProposalStart(MaxCallbackDTO $dto): void
    {
        $action = $dto->payload['action']; // feature/suggestion
        $proposalType = $action === 'feature' ? ProposalType::Feature : ProposalType::Suggestion;

        // Создаём состояние диалога, сохраняем userName для handleConfirm()
        $state = new ConversationState(
            userId: $dto->userId,
            currentStep: ConversationStep::AwaitingSubject,
            data: ['type' => $proposalType->value, 'user_name' => $dto->userName ?? ''],
        );
        $this->stateRepo->save($state);

        $this->answerCallbackIfPresent($dto);
        $this->maxBot->sendMessageWithInlineKeyboard(
            $dto->userId,
            "📌 Введите тему предложения:",
            [['text' => '❌ Отмена', 'payload' => ['action' => 'cancel', 'step' => 'awaiting_subject'], 'intent' => 'negative']],
        );
    }

    /**
     * Обработка кнопки «Вопрос ИИ» — создаёт сессию для ввода вопроса
     * После ввода вопроса — HandleDialogMessage опубликует его в RabbitMQ
     */
    private function handleRagQuery(MaxCallbackDTO $dto): void
    {
        $state = new ConversationState(
            userId: $dto->userId,
            currentStep: ConversationStep::AwaitingQuestion,
            data: [],
        );
        $this->stateRepo->save($state);

        $this->answerCallbackIfPresent($dto);
        $this->maxBot->sendMessageWithInlineKeyboard(
            $dto->userId,
            '🤖 Задайте вопрос:',
            [['text' => '❌ Отмена', 'payload' => ['action' => 'cancel', 'step' => 'awaiting_question'], 'intent' => 'negative']],
        );
    }

    /**
     * Подтверждение предложения — обновление inline-кнопки + задержка + главное меню
     */
    private function handleConfirm(MaxCallbackDTO $dto): void
    {
        $state = $this->stateRepo->findByUserId($dto->userId);

        if (!$this->isStepValid($dto, $state)) {
            $this->answerCallbackIfPresent($dto, '⏰ Сессия истекла или устаревшая кнопка');
            $this->mainMenuSender->send($dto->userId);
            return;
        }

        $type = $state->getData()['type'] ?? 'feature';
        $subject = $state->getData()['subject'] ?? '';
        $content = $state->getData()['content'] ?? '';

        $previewText = "📋 Превью предложения:\n"
            . "📌 Тема: {$subject}\n"
            . "📝 Описание: {$content}";

        if ($dto->callbackId !== null && $dto->callbackId !== '') {
            $this->maxBot->answerCallbackWithMessage(
                $dto->callbackId,
                $previewText,
                [['text' => '✅ Подтверждено', 'payload' => [], 'intent' => 'positive']],
            );
        } else {
            $this->maxBot->sendMessageToUser($dto->userId, $previewText);
        }

        try {
            $proposalType = ProposalType::from($type);
            $this->processProposal->execute(
                new ProposalDTO(
                    type: $proposalType,
                    userId: $dto->userId,
                    userName: (string)($state->getData()['user_name'] ?? ''),
                    subject: $subject,
                    content: $content,
                ),
            );
        } catch (RuntimeException $e) {
            $this->maxBot->sendMessageToUser($dto->userId, '❌ Произошла ошибка, попробуйте позже');
            $this->logger->error("Ошибка при подтверждении предложения: " . $e->getMessage());
            $this->mainMenuSender->send($dto->userId);
            return;
        }

        // Сбрасываем состояние
        $state->reset();
        $this->stateRepo->save($state);

        $this->mainMenuSender->send($dto->userId);
    }

    /**
     * Отмена — сброс состояния + возврат в главное меню
     * Отмена всегда безопасна — даже если step не совпадает, просто сбрасываем состояние
     */
    private function handleCancel(MaxCallbackDTO $dto): void
    {
        $state = $this->stateRepo->findByUserId($dto->userId);

        // Устаревшая кнопка — отменяем без уведомления, просто возвращаем в меню
        $payloadStep = $dto->payload['step'] ?? null;
        if ($payloadStep !== null && ($state === null || $state->isExpired() || $payloadStep !== $state->getCurrentStep()->value)) {
            $this->answerCallbackIfPresent($dto);
            $this->mainMenuSender->send($dto->userId);
            return;
        }

        $this->stateRepo->deleteByUserId($dto->userId);
        $this->answerCallbackIfPresent($dto, '❌ Отменено');
        $this->mainMenuSender->send($dto->userId);
    }
}
