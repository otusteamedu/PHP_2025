<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Application\UseCase;

use DateTimeImmutable;
use MkdBot\Application\DTO\MaxCallbackDTO;
use MkdBot\Application\Service\MainMenuSender;
use MkdBot\Application\UseCase\GetContacts;
use MkdBot\Application\UseCase\HandleMessageCallback;
use MkdBot\Application\UseCase\ProcessProposal;
use MkdBot\Domain\Enum\ConversationStep;
use MkdBot\Domain\Interface\ConversationStateRepositoryInterface;
use MkdBot\Domain\Interface\MaxBotClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;

class HandleMessageCallbackTest extends TestCase
{
    public function testContactsUkButton(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $getContacts->expects($this->once())->method('execute')->with('uk')->willReturn('UK contacts text');
        $maxBot->expects($this->once())->method('answerCallbackNotification')->with('cb.1', '✅');
        $maxBot->expects($this->once())->method('sendMessageToUser')->with(123, 'UK contacts text');
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.1',
            payload: ['action' => 'contacts', 'type' => 'uk'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    public function testContactsCouncilButton(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $getContacts->expects($this->once())->method('execute')->with('council')->willReturn('Council contacts text');
        $maxBot->expects($this->once())->method('answerCallbackNotification')->with('cb.2', '✅');
        $maxBot->expects($this->once())->method('sendMessageToUser')->with(123, 'Council contacts text');
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.2',
            payload: ['action' => 'contacts', 'type' => 'council'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    public function testCancelButtonWithValidStepDeletesState(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        // Создаём состояние с шагом awaiting_subject — совпадает с payload.step
        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: \MkdBot\Domain\Enum\ConversationStep::AwaitingSubject,
            data: ['type' => 'feature'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);
        $stateRepo->expects($this->once())->method('deleteByUserId')->with(123);
        $maxBot->expects($this->once())->method('answerCallbackNotification')->with('cb.1', '❌ Отменено');
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.1',
            payload: ['action' => 'cancel', 'step' => 'awaiting_subject'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    public function testCancelButtonWithInvalidStepReturnsToMenu(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        // Состояние отсутствует — step не совпадает
        $stateRepo->method('findByUserId')->with(123)->willReturn(null);
        // Не ожидаем deleteByUserId — сессия уже не существует
        $stateRepo->expects($this->never())->method('deleteByUserId');
        $maxBot->expects($this->once())->method('answerCallbackNotification')->with('cb.1', '✅');
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.1',
            payload: ['action' => 'cancel', 'step' => 'awaiting_subject'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    public function testRagQueryCreatesAwaitingQuestionState(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        // Ожидаем: сохранение состояния с шагом AwaitingQuestion
        $stateRepo->expects($this->once())->method('save')
            ->with($this->callback(function (\MkdBot\Domain\Entity\ConversationState $state) {
                return $state->getUserId() === 123
                    && $state->getCurrentStep() === ConversationStep::AwaitingQuestion;
            }));
        $maxBot->expects($this->once())->method('answerCallbackNotification');
        $maxBot->expects($this->once())->method('sendMessageWithInlineKeyboard')
            ->with(123, $this->stringContains('Задайте вопрос'), $this->anything());
        // Главное меню НЕ отправляется — пользователь в режиме ввода вопроса
        $mainMenuSender->expects($this->never())->method('send');

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.1',
            payload: ['action' => 'rag_query', 'step' => 'menu'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    public function testFeatureButtonStartsProposalFlow(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        // Проверяем, что в сохраняемое состояние включён user_name из callback
        $stateRepo->expects($this->once())->method('save')->with($this->callback(function (\MkdBot\Domain\Entity\ConversationState $state) {
            return $state->getData()['user_name'] === 'Иван'
                && $state->getData()['type'] === 'feature'
                && $state->getCurrentStep()->value === 'awaiting_subject';
        }));
        $maxBot->expects($this->once())->method('answerCallbackNotification')->with('cb.1', '✅');
        $maxBot->expects($this->once())->method('sendMessageWithInlineKeyboard');

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.1',
            payload: ['action' => 'feature', 'step' => 'menu'],
            userId: 123,
            chatId: 456,
            userName: 'Иван',
        );

        $useCase->execute($dto);
    }

    public function testSuggestionButtonStartsProposalFlowWithUserName(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        // Проверяем, что suggestion-тип и user_name сохраняются корректно
        $stateRepo->expects($this->once())->method('save')->with($this->callback(function (\MkdBot\Domain\Entity\ConversationState $state) {
            return $state->getData()['user_name'] === 'Ольга'
                && $state->getData()['type'] === 'suggestion'
                && $state->getCurrentStep()->value === 'awaiting_subject';
        }));
        $maxBot->expects($this->once())->method('answerCallbackNotification')->with('cb.2', '✅');
        $maxBot->expects($this->once())->method('sendMessageWithInlineKeyboard');

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.2',
            payload: ['action' => 'suggestion', 'step' => 'menu'],
            userId: 456,
            chatId: 789,
            userName: 'Ольга',
        );

        $useCase->execute($dto);
    }

    public function testConfirmGetsUserNameFromStateData(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        // Состояние с user_name, subject, content — как после полного flow ввода
        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: \MkdBot\Domain\Enum\ConversationStep::Preview,
            data: ['type' => 'feature', 'user_name' => 'Иван', 'subject' => 'Тема', 'content' => 'Описание'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        // После подтверждения состояние сбрасывается
        $stateRepo->expects($this->once())->method('save');

        $maxBot->expects($this->once())->method('answerCallbackWithMessage');
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        // Проверяем, что ProcessProposal получает userName из состояния, а не из payload
        $processProposal->expects($this->once())->method('execute')->with($this->callback(function (\MkdBot\Application\DTO\ProposalDTO $dto) {
            return $dto->userName === 'Иван'
                && $dto->subject === 'Тема'
                && $dto->content === 'Описание'
                && $dto->type->value === 'feature';
        }));

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        // В payload НЕТ user_name — как в реальном callback кнопки «Подтвердить»
        $dto = new MaxCallbackDTO(
            callbackId: 'cb.confirm',
            payload: ['action' => 'confirm', 'type' => 'feature', 'step' => 'preview'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    public function testUnknownActionLogsWarning(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $logger->expects($this->once())->method('warning')->with($this->stringContains('Неизвестное действие callback'));
        $maxBot->expects($this->never())->method('answerCallbackNotification');

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.1',
            payload: ['action' => 'unknown_action'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    // ========== Крайние случаи ==========

    /**
     * Подтверждение предложения (confirm) — вызов answerCallbackWithMessage()
     * с обновлённой клавиатурой (✅ Подтверждено), затем sleep(1),
     * затем новое сообщение с главным меню
     */
    public function testConfirmUpdatesKeyboardAndShowsMainMenu(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: \MkdBot\Domain\Enum\ConversationStep::Preview,
            data: ['type' => 'feature', 'user_name' => 'Иван', 'subject' => 'Тема', 'content' => 'Описание'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        // Проверяем answerCallbackWithMessage с клавиатурой «✅ Подтверждено»
        $maxBot->expects($this->once())->method('answerCallbackWithMessage')
            ->with(
                'cb.confirm',
                $this->stringContains('Тема'),
                $this->callback(function (array $buttons) {
                    return $buttons[0]['text'] === '✅ Подтверждено'
                        && $buttons[0]['intent'] === 'positive';
                }),
            );

        // После sleep(1) — главное меню через MainMenuSender
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        // ProcessProposal вызывается
        $processProposal->expects($this->once())->method('execute');

        // Состояние сбрасывается и сохраняется
        $stateRepo->expects($this->once())->method('save');

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.confirm',
            payload: ['action' => 'confirm', 'type' => 'feature', 'step' => 'preview'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    /**
     * Тип suggestion — кнопка «Предложение совету дома» начинает flow с ProposalType::Suggestion
     */
    public function testSuggestionButtonStartsFlowWithSuggestionType(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        // Проверяем, что тип предложения — именно suggestion
        $stateRepo->expects($this->once())->method('save')
            ->with($this->callback(function (\MkdBot\Domain\Entity\ConversationState $state) {
                return $state->getData()['type'] === 'suggestion'
                    && $state->getCurrentStep()->value === 'awaiting_subject';
            }));
        $maxBot->expects($this->once())->method('answerCallbackNotification')->with('cb.sugg', '✅');
        $maxBot->expects($this->once())->method('sendMessageWithInlineKeyboard')
            ->with(
                123,
                $this->stringContains('Введите тему предложения'),
                $this->anything(),
            );

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.sugg',
            payload: ['action' => 'suggestion', 'step' => 'menu'],
            userId: 123,
            chatId: 456,
            userName: 'Пользователь',
        );

        $useCase->execute($dto);
    }

    /**
     * Отмена на шаге ввода темы — callback с action=cancel, step=awaiting_subject
     * -> сброс состояния + главное меню
     */
    public function testCancelOnAwaitingSubjectStep(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: \MkdBot\Domain\Enum\ConversationStep::AwaitingSubject,
            data: ['type' => 'feature'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        // Состояние удаляется
        $stateRepo->expects($this->once())->method('deleteByUserId')->with(123);
        // Уведомление «❌ Отменено»
        $maxBot->expects($this->once())->method('answerCallbackNotification')->with('cb.cancel', '❌ Отменено');
        // Главное меню
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.cancel',
            payload: ['action' => 'cancel', 'step' => 'awaiting_subject'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    /**
     * Отмена на шаге ввода описания — callback с action=cancel, step=awaiting_description
     * -> сброс состояния + главное меню
     */
    public function testCancelOnAwaitingDescriptionStep(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: \MkdBot\Domain\Enum\ConversationStep::AwaitingDescription,
            data: ['type' => 'feature', 'subject' => 'Тема предложения'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        $stateRepo->expects($this->once())->method('deleteByUserId')->with(123);
        $maxBot->expects($this->once())->method('answerCallbackNotification')->with('cb.cancel', '❌ Отменено');
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.cancel',
            payload: ['action' => 'cancel', 'step' => 'awaiting_description'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    /**
     * Отмена на шаге превью — callback с action=cancel, step=preview
     * -> сброс состояния + главное меню
     */
    public function testCancelOnPreviewStep(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: \MkdBot\Domain\Enum\ConversationStep::Preview,
            data: ['type' => 'feature', 'subject' => 'Тема', 'content' => 'Описание'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        $stateRepo->expects($this->once())->method('deleteByUserId')->with(123);
        $maxBot->expects($this->once())->method('answerCallbackNotification')->with('cb.cancel', '❌ Отменено');
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.cancel',
            payload: ['action' => 'cancel', 'step' => 'preview'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    /**
     * Истёкшая сессия при callback — состояние истекло
     * -> «⏰ Сессия истекла или устаревшая кнопка» + главное меню
     */
    public function testExpiredSessionOnCallbackShowsExpiredMessage(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        // Состояние с истёкшим expires_at
        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: \MkdBot\Domain\Enum\ConversationStep::Preview,
            data: ['type' => 'feature', 'subject' => 'Тема', 'content' => 'Описание'],
            updatedAt: new DateTimeImmutable('-31 minutes'),
            expiresAt: new DateTimeImmutable('-1 minute'),
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        // isStepValid вернёт false — сессия истекла
        $maxBot->expects($this->once())->method('answerCallbackNotification')
            ->with('cb.confirm', '⏰ Сессия истекла или устаревшая кнопка');
        $mainMenuSender->expects($this->once())->method('send')->with(123);
        // ProcessProposal НЕ должен вызываться
        $processProposal->expects($this->never())->method('execute');

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.confirm',
            payload: ['action' => 'confirm', 'type' => 'feature', 'step' => 'preview'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    /**
     * Кнопка «Вопрос ИИ» — создаёт сессию AwaitingQuestion + inline-клавиатура с отменой
     */
    public function testRagQueryButtonCreatesAwaitingQuestionWithKeyboard(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $stateRepo->expects($this->once())->method('save');
        $maxBot->expects($this->once())->method('answerCallbackNotification')->with('cb.rag', '✅');
        $maxBot->expects($this->once())->method('sendMessageWithInlineKeyboard')
            ->with(123, $this->stringContains('Задайте вопрос'), $this->anything());
        $mainMenuSender->expects($this->never())->method('send');

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.rag',
            payload: ['action' => 'rag_query', 'step' => 'menu'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    /**
     * Неизвестный action — логируется предупреждение, бот не отвечает
     */
    public function testUnknownActionIsIgnoredWithWarning(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $logger->expects($this->once())->method('warning')
            ->with($this->stringContains('Неизвестное действие callback: totally_unknown'));
        // Бот не должен отправлять никаких сообщений
        $maxBot->expects($this->never())->method('answerCallbackNotification');
        $maxBot->expects($this->never())->method('sendMessageToUser');
        $mainMenuSender->expects($this->never())->method('send');

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.unknown',
            payload: ['action' => 'totally_unknown'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    /**
     * Подтверждение с ошибкой в ProcessProposal — пользователь получает
     * «❌ Произошла ошибка, попробуйте позже» + главное меню
     */
    public function testConfirmWithProcessProposalErrorShowsErrorMessage(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: \MkdBot\Domain\Enum\ConversationStep::Preview,
            data: ['type' => 'feature', 'user_name' => 'Иван', 'subject' => 'Тема', 'content' => 'Описание'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        // ProcessProposal выбрасывает RuntimeException
        $processProposal->expects($this->once())->method('execute')
            ->willThrowException(new RuntimeException('DB error'));

        // answerCallbackWithMessage вызывается до ошибки
        $maxBot->expects($this->once())->method('answerCallbackWithMessage');
        // Сообщение об ошибке
        $maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, '❌ Произошла ошибка, попробуйте позже');
        // Главное меню
        $mainMenuSender->expects($this->once())->method('send')->with(123);
        // Ошибка логируется
        $logger->expects($this->once())->method('error');

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.confirm',
            payload: ['action' => 'confirm', 'type' => 'feature', 'step' => 'preview'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    /**
     * Отмена с истёкшей сессией — step не совпадает (сессия истекла)
     * -> answerCallbackNotification('✅') + главное меню (без «Отменено»)
     */
    public function testCancelWithExpiredSessionReturnsToMenuWithoutCancelMessage(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        // Состояние с истёкшим expires_at — step не совпадёт
        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: \MkdBot\Domain\Enum\ConversationStep::AwaitingSubject,
            data: ['type' => 'feature'],
            updatedAt: new DateTimeImmutable('-31 minutes'),
            expiresAt: new DateTimeImmutable('-1 minute'),
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        // Уведомление без «Отменено» — просто ✅
        $maxBot->expects($this->once())->method('answerCallbackNotification')->with('cb.cancel', '✅');
        $mainMenuSender->expects($this->once())->method('send')->with(123);
        // deleteByUserId НЕ вызывается — сессия устарела
        $stateRepo->expects($this->never())->method('deleteByUserId');

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.cancel',
            payload: ['action' => 'cancel', 'step' => 'awaiting_subject'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    // ========== callbackId пустой — fallback на sendMessage ==========

    /**
     * Контакты УК с пустым callbackId — answerCallbackNotification НЕ вызывается,
     * вместо этого отправляется sendMessageToUser
     */
    public function testContactsWithNullCallbackIdSkipsAnswerCallback(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $getContacts->expects($this->once())->method('execute')->with('uk')->willReturn('UK contacts text');
        // answerCallbackNotification НЕ вызывается — callbackId пустой
        $maxBot->expects($this->never())->method('answerCallbackNotification');
        $maxBot->expects($this->once())->method('sendMessageToUser')->with(123, 'UK contacts text');
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: null,
            payload: ['action' => 'contacts', 'type' => 'uk'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    /**
     * Feature-кнопка с пустым callbackId — answerCallbackNotification НЕ вызывается,
     * sendMessageWithInlineKeyboard отправляется
     */
    public function testFeatureButtonWithNullCallbackIdSkipsAnswerCallback(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $stateRepo->expects($this->once())->method('save');
        // answerCallbackNotification НЕ вызывается — callbackId пустой
        $maxBot->expects($this->never())->method('answerCallbackNotification');
        $maxBot->expects($this->once())->method('sendMessageWithInlineKeyboard');

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: null,
            payload: ['action' => 'feature', 'step' => 'menu'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    /**
     * Confirm с пустым callbackId — answerCallbackWithMessage НЕ вызывается,
     * вместо этого отправляется sendMessageToUser с превью
     */
    public function testConfirmWithNullCallbackIdSendsMessageInsteadOfAnswerCallback(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: \MkdBot\Domain\Enum\ConversationStep::Preview,
            data: ['type' => 'feature', 'user_name' => 'Иван', 'subject' => 'Тема', 'content' => 'Описание'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);
        $stateRepo->expects($this->once())->method('save');

        // answerCallbackWithMessage НЕ вызывается — callbackId пустой
        $maxBot->expects($this->never())->method('answerCallbackWithMessage');
        // Вместо этого отправляется sendMessageToUser с превью
        $maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, $this->stringContains('Тема'));
        // Главное меню после sleep
        $mainMenuSender->expects($this->once())->method('send')->with(123);
        $processProposal->expects($this->once())->method('execute');

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: null,
            payload: ['action' => 'confirm', 'type' => 'feature', 'step' => 'preview'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    /**
     * Cancel с пустым callbackId — answerCallbackNotification НЕ вызывается,
     * состояние удаляется, главное меню отправляется
     */
    public function testCancelWithNullCallbackIdSkipsAnswerCallback(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: \MkdBot\Domain\Enum\ConversationStep::AwaitingSubject,
            data: ['type' => 'feature'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);
        $stateRepo->expects($this->once())->method('deleteByUserId')->with(123);
        // answerCallbackNotification НЕ вызывается — callbackId пустой
        $maxBot->expects($this->never())->method('answerCallbackNotification');
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: null,
            payload: ['action' => 'cancel', 'step' => 'awaiting_subject'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }

    /**
     * RagQuery с пустым callbackId — answerCallbackNotification НЕ вызывается,
     * но состояние создаётся и inline-клавиатура отправляется
     */
    public function testRagQueryWithNullCallbackIdSkipsAnswerCallback(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $processProposal = $this->createMock(ProcessProposal::class);
        $getContacts = $this->createMock(GetContacts::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $stateRepo->expects($this->once())->method('save');
        // answerCallbackNotification НЕ вызывается — callbackId пустой
        $maxBot->expects($this->never())->method('answerCallbackNotification');
        $maxBot->expects($this->once())->method('sendMessageWithInlineKeyboard');
        $mainMenuSender->expects($this->never())->method('send');

        $useCase = new HandleMessageCallback($stateRepo, $maxBot, $processProposal, $getContacts, $logger, $mainMenuSender);

        $dto = new MaxCallbackDTO(
            callbackId: null,
            payload: ['action' => 'rag_query', 'step' => 'menu'],
            userId: 123,
            chatId: 456,
        );

        $useCase->execute($dto);
    }
}
