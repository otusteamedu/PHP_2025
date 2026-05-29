<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Application\UseCase;

use DateTimeImmutable;
use MkdBot\Application\DTO\MaxMessageDTO;
use MkdBot\Application\Service\MainMenuSender;
use MkdBot\Application\UseCase\HandleDialogMessage;
use MkdBot\Domain\Enum\ConversationStep;
use MkdBot\Domain\Interface\ConversationStateRepositoryInterface;
use MkdBot\Domain\Interface\MaxBotClientInterface;
use MkdBot\Domain\Interface\QueuePublisherInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class HandleDialogMessageTest extends TestCase
{
    public function testStartCommandResetsStateAndShowsMenu(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $stateRepo->expects($this->once())->method('deleteByUserId')->with(123);
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: '/start',
            mid: 'mid.2',
            chatId: 456,
            chatType: 'dialog',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    public function testHelpCommandShowsMenu(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $stateRepo->expects($this->once())->method('deleteByUserId')->with(123);
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: '/help',
            mid: 'mid.3',
            chatId: 456,
            chatType: 'dialog',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    public function testNoActiveSessionShowsMenu(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $stateRepo->method('findByUserId')->willReturn(null);
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: 'hello',
            mid: 'mid.4',
            chatId: 456,
            chatType: 'dialog',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    public function testExpiredSessionShowsMenu(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = $this->createMock(\MkdBot\Domain\Entity\ConversationState::class);
        $state->method('isExpired')->willReturn(true);

        $stateRepo->method('findByUserId')->willReturn($state);
        $stateRepo->expects($this->once())->method('deleteByUserId')->with(123);
        $maxBot->expects($this->once())->method('sendMessageToUser')->with(123, '⏰ Сессия истекла, начните заново');
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: 'some text',
            mid: 'mid.5',
            chatId: 456,
            chatType: 'dialog',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    public function testSubjectInputSavesUserNameInState(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        // Реальное состояние — шаг awaiting_subject, тип feature
        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: ConversationStep::AwaitingSubject,
            data: ['type' => 'feature'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        // Проверяем, что при сохранении состояния в data появился user_name
        $stateRepo->expects($this->once())->method('save')->with($this->callback(function (\MkdBot\Domain\Entity\ConversationState $s) {
            return $s->getData()['user_name'] === 'Иван'
                && $s->getData()['subject'] === 'Новая тема'
                && $s->getCurrentStep() === ConversationStep::AwaitingDescription;
        }));
        $maxBot->expects($this->once())->method('sendMessageWithInlineKeyboard');

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'Иван',
            text: 'Новая тема',
            mid: 'mid.6',
            chatId: 456,
            chatType: 'dialog',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    // ========== Крайние случаи ==========

    /**
     * Истёкшая сессия — пользователь с истёкшим expires_at получает
     * «⏰ Сессия истекла, начните заново» + главное меню
     */
    public function testExpiredSessionShowsExpiredMessageAndMenu(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        // Состояние с истёкшим expires_at
        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: ConversationStep::AwaitingSubject,
            data: ['type' => 'feature'],
            updatedAt: new DateTimeImmutable('-31 minutes'),
            expiresAt: new DateTimeImmutable('-1 minute'),
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        // Проверяем: 1) уведомление об истечении, 2) удаление состояния, 3) главное меню
        $maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, '⏰ Сессия истекла, начните заново');
        $stateRepo->expects($this->once())->method('deleteByUserId')->with(123);
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: 'some text',
            mid: 'mid.exp',
            chatId: 456,
            chatType: 'dialog',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Пустое сообщение с вложениями (фото) без текста
     * на шаге awaiting_subject -> «Пожалуйста, введите текст»
     */
    public function testEmptyMessageWithPhotoOnAwaitingSubject(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: ConversationStep::AwaitingSubject,
            data: ['type' => 'feature'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        // Ожидаем сообщение с просьбой ввести текст
        $maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, 'Пожалуйста, введите текст');
        // Не должно быть сохранения состояния
        $stateRepo->expects($this->never())->method('save');

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: '',
            mid: 'mid.photo',
            chatId: 456,
            chatType: 'dialog',
            attachments: [['type' => 'photo', 'url' => 'https://max.ru/photo.jpg', 'token' => null, 'filename' => null, 'size' => null]],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Пустое сообщение с вложениями (документ) без текста
     * на шаге awaiting_description -> «Пожалуйста, введите текст»
     */
    public function testEmptyMessageWithDocumentOnAwaitingDescription(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: ConversationStep::AwaitingDescription,
            data: ['type' => 'feature', 'subject' => 'Тема'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        // Пустой текст -> «Пожалуйста, введите текст»
        $maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, 'Пожалуйста, введите текст');
        $stateRepo->expects($this->never())->method('save');

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: '',
            mid: 'mid.doc',
            chatId: 456,
            chatType: 'dialog',
            attachments: [['type' => 'file', 'url' => 'https://max.ru/doc.pdf', 'token' => null, 'filename' => 'doc.pdf', 'size' => 1024]],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Лимит длины темы — тема > 200 символов ->
     * «❌ Тема слишком длинная, максимум 200 символов. Пожалуйста, введите тему заново:»
     */
    public function testSubjectExceedsMaxLength(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: ConversationStep::AwaitingSubject,
            data: ['type' => 'feature'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        // Ожидаем сообщение об ошибке длины
        $maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, '❌ Тема слишком длинная, максимум 200 символов. Пожалуйста, введите тему заново:');
        // Состояние не должно сохраняться
        $stateRepo->expects($this->never())->method('save');

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $longSubject = str_repeat('а', 201);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: $longSubject,
            mid: 'mid.long',
            chatId: 456,
            chatType: 'dialog',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Тема ровно 200 символов — должна пройти успешно
     */
    public function testSubjectExactlyMaxLengthPasses(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: ConversationStep::AwaitingSubject,
            data: ['type' => 'feature'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        // Состояние должно быть сохранено с темой
        $stateRepo->expects($this->once())->method('save');
        $maxBot->expects($this->once())->method('sendMessageWithInlineKeyboard');

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $exactSubject = str_repeat('а', 200);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: $exactSubject,
            mid: 'mid.exact',
            chatId: 456,
            chatType: 'dialog',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Лимит длины описания — описание > 3000 символов ->
     * «❌ Описание слишком длинное, максимум 3000 символов. Пожалуйста, введите описание заново:»
     */
    public function testDescriptionExceedsMaxLength(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: ConversationStep::AwaitingDescription,
            data: ['type' => 'feature', 'subject' => 'Тема'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        // Ожидаем сообщение об ошибке длины описания
        $maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, '❌ Описание слишком длинное, максимум 3000 символов. Пожалуйста, введите описание заново:');
        $stateRepo->expects($this->never())->method('save');

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $longDescription = str_repeat('б', 3001);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: $longDescription,
            mid: 'mid.longdesc',
            chatId: 456,
            chatType: 'dialog',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Описание ровно 3000 символов — должно пройти успешно
     */
    public function testDescriptionExactlyMaxLengthPasses(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: ConversationStep::AwaitingDescription,
            data: ['type' => 'feature', 'subject' => 'Тема'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        $stateRepo->expects($this->once())->method('save');
        $maxBot->expects($this->once())->method('sendMessageWithInlineKeyboard');

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $exactDescription = str_repeat('б', 3000);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: $exactDescription,
            mid: 'mid.exactdesc',
            chatId: 456,
            chatType: 'dialog',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Команда /start во время ввода темы -> сброс состояния + главное меню
     */
    public function testStartCommandDuringSubjectInputResetsState(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        // /start обрабатывается ДО проверки состояния — состояние даже не запрашивается
        $stateRepo->expects($this->never())->method('findByUserId');
        $stateRepo->expects($this->once())->method('deleteByUserId')->with(123);
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: '/start',
            mid: 'mid.start',
            chatId: 456,
            chatType: 'dialog',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Команда /help во время ввода описания -> сброс состояния + главное меню
     */
    public function testHelpCommandDuringDescriptionInputResetsState(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        // /help обрабатывается ДО проверки состояния
        $stateRepo->expects($this->never())->method('findByUserId');
        $stateRepo->expects($this->once())->method('deleteByUserId')->with(123);
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: '/help',
            mid: 'mid.help',
            chatId: 456,
            chatType: 'dialog',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Сообщение без текста (пустой текст) на шаге awaiting_subject
     * -> «Пожалуйста, введите текст»
     */
    public function testEmptyTextOnAwaitingSubjectRequestsTextInput(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: ConversationStep::AwaitingSubject,
            data: ['type' => 'feature'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        $maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, 'Пожалуйста, введите текст');
        $stateRepo->expects($this->never())->method('save');

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: '',
            mid: 'mid.empty',
            chatId: 456,
            chatType: 'dialog',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Сообщение без текста (пробелы) на шаге awaiting_description
     * -> «Пожалуйста, введите текст»
     */
    public function testWhitespaceOnlyTextOnAwaitingDescriptionRequestsTextInput(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: ConversationStep::AwaitingDescription,
            data: ['type' => 'feature', 'subject' => 'Тема'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        $maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, 'Пожалуйста, введите текст');
        $stateRepo->expects($this->never())->method('save');

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: ' ',
            mid: 'mid.ws',
            chatId: 456,
            chatType: 'dialog',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Сообщение из группового чата (chatType = 'chat') — не channel,
     * поэтому обрабатывается как диалоговое; userId=123, нет сессии -> главное меню
     */
    public function testChatTypeMessageDoesNotForwardNotChannel(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        // chatType='chat' не равно 'channel', поэтому код идёт дальше
        $stateRepo->method('findByUserId')->willReturn(null);
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: 'Chat message',
            mid: 'mid.chat',
            chatId: 789,
            chatType: 'chat',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Пустое сообщение на шаге awaiting_question -> «Пожалуйста, введите текст»
     */
    public function testEmptyMessageOnAwaitingQuestion(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: ConversationStep::AwaitingQuestion,
            data: [],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        $maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, 'Пожалуйста, введите текст');

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: '',
            mid: 'mid.nontext',
            chatId: 456,
            chatType: 'dialog',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * AwaitingQuestion — текстовый ввод -> «Функция в разработке» + reset + главное меню
     * Покрывает handleQuestionInput()
     */
    public function testAwaitingQuestionTextInputShowsFeatureInDevelopment(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: ConversationStep::AwaitingQuestion,
            data: [],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        // Ожидаем: sendMessageToUser (функция в разработке) + главное меню через MainMenuSender
        $maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, '🔧 Функция в разработке');
        $mainMenuSender->expects($this->once())->method('send')->with(123);

        // Ожидаем сохранение состояния после reset
        $stateRepo->expects($this->once())->method('save');

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: 'Как оплатить услуги?',
            mid: 'mid.question',
            chatId: 456,
            chatType: 'dialog',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Описание слишком длинное — ошибка + повторный ввод описания
     * Покрывает ветку mb_strlen > DESCRIPTION_MAX_LENGTH в handleDescriptionInput
     */
    public function testDescriptionTooLongShowsErrorMessage(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: ConversationStep::AwaitingDescription,
            data: ['type' => 'feature', 'subject' => 'Тема'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        // Ожидаем сообщение об ошибке — описание слишком длинное
        $maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, $this->stringContains('слишком длинное'));
        // Не ожидаем sendMessageWithInlineKeyboard (нет перехода к превью)
        $maxBot->expects($this->never())->method('sendMessageWithInlineKeyboard');

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $longDescription = str_repeat('А', 3001);
        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: $longDescription,
            mid: 'mid.longdesc',
            chatId: 456,
            chatType: 'dialog',
            attachments: [],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Текст + вложения на шаге awaiting_subject
     * -> текст принимается, показывается предупреждение о вложениях
     */
    public function testTextWithAttachmentsShowsWarningButAcceptsText(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $state = new \MkdBot\Domain\Entity\ConversationState(
            userId: 123,
            currentStep: ConversationStep::AwaitingSubject,
            data: ['type' => 'feature'],
        );
        $stateRepo->method('findByUserId')->with(123)->willReturn($state);

        // Ожидаем: 1) предупреждение о вложениях, 2) клавиатура следующего шага (текст принят)
        $maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, '📎 Вложения не поддерживаются, принят только текст');
        $maxBot->expects($this->once())->method('sendMessageWithInlineKeyboard');
        // Состояние должно быть сохранено (текст принят как тема)
        $stateRepo->expects($this->once())->method('save');

        $useCase = new HandleDialogMessage($stateRepo, $maxBot, $logger, $mainMenuSender);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'User',
            text: 'Новая тема с вложением',
            mid: 'mid.textattach',
            chatId: 456,
            chatType: 'dialog',
            attachments: [['type' => 'photo', 'url' => 'https://max.ru/photo.jpg', 'token' => null, 'filename' => null, 'size' => null]],
            messageUrl: null,
        );

        $useCase->execute($dto);
    }
}
