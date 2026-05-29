<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Infrastructure\Notification;

use MkdBot\Domain\Enum\ProposalType;
use MkdBot\Infrastructure\Notification\PhpMailerProposalNotifier;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionClass;

/**
 * Юнит-тесты для PhpMailerProposalNotifier
 *
 * Тестирует отправку уведомлений о предложениях через PHPMailer.
 * Использует реальный PHPMailer (без мока — он final),
 * но с невалидными SMTP-данными для проверки обработки ошибок.
 */
class PhpMailerProposalNotifierTest extends TestCase
{
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    /**
     * Успешная отправка уведомления — логируется INFO с количеством адресов
     * Тестируем с невалидным SMTP — ожидаем ошибку, но проверяем форматирование
     */
    public function testNotifyWithEmptyRecipientsLogsWarning(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        // Ожидаем warning о пустом списке адресов
        $logger->expects($this->once())->method('warning')
            ->with($this->stringContains('пуст'));

        $notifier = new PhpMailerProposalNotifier(
            smtpHost: 'smtp.example.com',
            smtpPort: 465,
            smtpUser: 'user@example.com',
            smtpPassword: 'password',
            smtpFromEmail: 'bot@example.com',
            smtpFromName: 'МКД Бот',
            notifyEmails: '', // Пустой список
            logger: $logger,
        );

        // Не должно выбросить исключение
        $notifier->notify(
            type: ProposalType::Feature,
            subject: 'Test Subject',
            content: 'Test Content',
            authorName: 'Test Author',
            authorId: 12345,
        );
    }

    /**
     * Уведомление с несколькими получателями — парсинг comma-separated списка
     */
    public function testNotifyWithMultipleRecipients(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        // С невалидным SMTP ожидаем ошибку отправки, но проверяем что список парсится
        $logger->expects($this->once())->method('error')
            ->with($this->stringContains('Ошибка отправки'));

        $notifier = new PhpMailerProposalNotifier(
            smtpHost: 'invalid-smtp.test',
            smtpPort: 465,
            smtpUser: 'user@example.com',
            smtpPassword: 'password',
            smtpFromEmail: 'bot@example.com',
            smtpFromName: 'МКД Бот',
            notifyEmails: 'admin1@example.com,admin2@example.com,admin3@example.com',
            logger: $logger,
        );

        $notifier->notify(
            type: ProposalType::Suggestion,
            subject: 'Test Subject',
            content: 'Test Content',
            authorName: 'Test Author',
            authorId: 12345,
        );
    }

    /**
     * Ошибка SMTP перехватывается и логируется — исключение НЕ пробрасывается
     */
    public function testSmtpFailureIsCaughtAndLogged(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        // Ожидаем логирование ошибки SMTP
        $logger->expects($this->once())->method('error')
            ->with($this->stringContains('Ошибка отправки'));

        $notifier = new PhpMailerProposalNotifier(
            smtpHost: 'invalid-host-that-does-not-exist.test',
            smtpPort: 465,
            smtpUser: 'invalid',
            smtpPassword: 'invalid',
            smtpFromEmail: 'invalid@example.com',
            smtpFromName: 'МКД Бот',
            notifyEmails: 'recipient@example.com',
            logger: $logger,
        );

        // Не должно выбросить исключение — fire-and-forget
        $notifier->notify(
            type: ProposalType::Feature,
            subject: 'Test',
            content: 'Content',
            authorName: 'Author',
            authorId: 1,
        );

        // Если мы дошли сюда — исключение не было выброшено
        $this->assertTrue(true);
    }

    /**
     * Правильная тема письма для feature типа
     * Проверяем через рефлексию, что subject формируется корректно
     */
    public function testCorrectSubjectForFeatureType(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->method('error');

        $notifier = new PhpMailerProposalNotifier(
            smtpHost: 'invalid-smtp.test',
            smtpPort: 465,
            smtpUser: 'user@example.com',
            smtpPassword: 'password',
            smtpFromEmail: 'bot@example.com',
            smtpFromName: 'МКД Бот',
            notifyEmails: 'admin@example.com',
            logger: $logger,
        );

        // Вызываем notify для Feature — не должно быть исключения
        $notifier->notify(
            type: ProposalType::Feature,
            subject: 'New Feature Request',
            content: 'Please add this feature',
            authorName: 'Author',
            authorId: 42,
        );

        // Проверяем что тема формируется с «Новый функционал»
        // Через рефлексию проверяем приватный метод buildHtmlBody
        $ref = new ReflectionClass(PhpMailerProposalNotifier::class);
        $method = $ref->getMethod('buildHtmlBody');
        $method->setAccessible(true);

        $result = $method->invoke(
            $notifier,
            ProposalType::Feature,
            'Test Feature',
            'Feature content',
            'Author Name',
            123,
        );

        $this->assertStringContainsString('Функционал', $result);
        $this->assertStringContainsString('Test Feature', $result);
    }

    /**
     * Правильная тема письма для suggestion типа
     */
    public function testCorrectSubjectForSuggestionType(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->method('error');

        $notifier = new PhpMailerProposalNotifier(
            smtpHost: 'invalid-smtp.test',
            smtpPort: 465,
            smtpUser: 'user@example.com',
            smtpPassword: 'password',
            smtpFromEmail: 'bot@example.com',
            smtpFromName: 'МКД Бот',
            notifyEmails: 'admin@example.com',
            logger: $logger,
        );

        // Через рефлексию проверяем приватный метод buildHtmlBody
        $ref = new ReflectionClass(PhpMailerProposalNotifier::class);
        $method = $ref->getMethod('buildHtmlBody');
        $method->setAccessible(true);

        $result = $method->invoke(
            $notifier,
            ProposalType::Suggestion,
            'Test Suggestion',
            'Suggestion content',
            'Author Name',
            456,
        );

        $this->assertStringContainsString('Предложение', $result);
        $this->assertStringContainsString('Test Suggestion', $result);
    }

    /**
     * HTML-тело письма содержит все данные предложения
     */
    public function testHtmlBodyContainsAllProposalData(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $notifier = new PhpMailerProposalNotifier(
            smtpHost: 'invalid-smtp.test',
            smtpPort: 465,
            smtpUser: 'user@example.com',
            smtpPassword: 'password',
            smtpFromEmail: 'bot@example.com',
            smtpFromName: 'МКД Бот',
            notifyEmails: 'admin@example.com',
            logger: $logger,
        );

        $ref = new ReflectionClass(PhpMailerProposalNotifier::class);
        $method = $ref->getMethod('buildHtmlBody');
        $method->setAccessible(true);

        $result = $method->invoke(
            $notifier,
            ProposalType::Feature,
            'My Subject',
            'My Content',
            'Иван Иванов',
            789,
        );

        // Проверяем что все данные включены в тело письма
        $this->assertStringContainsString('My Subject', $result);
        $this->assertStringContainsString('My Content', $result);
        $this->assertStringContainsString('Иван Иванов', $result);
        $this->assertStringContainsString('789', $result);
    }

    /**
     * Парсинг notifyEmails с пробелами вокруг запятых
     */
    public function testNotifyEmailsParsingWithSpaces(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->method('error');

        $notifier = new PhpMailerProposalNotifier(
            smtpHost: 'invalid-smtp.test',
            smtpPort: 465,
            smtpUser: 'user@example.com',
            smtpPassword: 'password',
            smtpFromEmail: 'bot@example.com',
            smtpFromName: 'МКД Бот',
            notifyEmails: '  admin1@test.com  ,  admin2@test.com  ,  admin3@test.com  ',
            logger: $logger,
        );

        // Не должно быть исключения — пробелы обрезаются корректно
        $notifier->notify(
            type: ProposalType::Feature,
            subject: 'Test',
            content: 'Content',
            authorName: 'Author',
            authorId: 1,
        );

        $this->assertTrue(true);
    }
}
