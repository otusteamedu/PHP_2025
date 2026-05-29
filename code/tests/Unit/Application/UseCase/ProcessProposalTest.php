<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Application\UseCase;

use MkdBot\Application\DTO\ProposalDTO;
use MkdBot\Application\UseCase\ProcessProposal;
use MkdBot\Domain\Enum\ProposalType;
use MkdBot\Domain\Interface\ProposalNotifierInterface;
use MkdBot\Domain\Interface\ProposalRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Юнит-тесты для ProcessProposal
 *
 * Тестирует сохранение предложений (feature/suggestion),
 * обработку ошибок, формирование ProposalDTO,
 * а также вызов нотификатора после сохранения
 */
class ProcessProposalTest extends TestCase
{
    /**
     * Успешное сохранение предложения (feature type) — нотификатор вызывается
     */
    public function testSaveProposalSuccessfully(): void
    {
        $proposalRepo = $this->createMock(ProposalRepositoryInterface::class);
        $notifier = $this->createMock(ProposalNotifierInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        // Ожидаем сохранение в репозиторий
        $proposalRepo->expects($this->once())->method('save');

        // Не ожидаем отправку сообщения об ошибке

        // Ожидаем вызов нотификатора после сохранения
        $notifier->expects($this->once())->method('notify')
            ->with(
                ProposalType::Feature,
                'New feature',
                'Feature description',
                'Test User',
                12345,
            );

        $logger->method('info');

        $useCase = new ProcessProposal($proposalRepo, $notifier, $logger);

        $dto = new ProposalDTO(
            type: ProposalType::Feature,
            userId: 12345,
            userName: 'Test User',
            subject: 'New feature',
            content: 'Feature description',
        );

        $useCase->execute($dto);
    }

    /**
     * Успешное сохранение предложения (suggestion type) — нотификатор вызывается
     */
    public function testSaveSuggestionProposalSuccessfully(): void
    {
        $proposalRepo = $this->createMock(ProposalRepositoryInterface::class);
        $notifier = $this->createMock(ProposalNotifierInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        // Ожидаем сохранение в репозиторий
        $proposalRepo->expects($this->once())->method('save');

        $logger->expects($this->once())->method('info')->with($this->stringContains('suggestion'));

        // Ожидаем вызов нотификатора
        $notifier->expects($this->once())->method('notify')
            ->with(
                ProposalType::Suggestion,
                'Improve lighting',
                'Need better lights in hallway',
                'Suggester',
                99999,
            );

        $useCase = new ProcessProposal($proposalRepo, $notifier, $logger);

        $dto = new ProposalDTO(
            type: ProposalType::Suggestion,
            userId: 99999,
            userName: 'Suggester',
            subject: 'Improve lighting',
            content: 'Need better lights in hallway',
        );

        $useCase->execute($dto);
    }

    /**
     * Ошибка сохранения — RuntimeException -> логирование ERROR, нотификатор НЕ вызывается
     */
    public function testSaveProposalErrorThrowsRuntimeException(): void
    {
        $proposalRepo = $this->createMock(ProposalRepositoryInterface::class);
        $notifier = $this->createMock(ProposalNotifierInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        // Репозиторий выбрасывает исключение
        $proposalRepo->method('save')->willThrowException(new RuntimeException('DB error'));

        // Не ожидаем отправку сообщения — теперь UseCase выбрасывает исключение

        // Нотификатор НЕ должен вызываться при ошибке сохранения
        $notifier->expects($this->never())->method('notify');

        $logger->expects($this->once())->method('error');

        $useCase = new ProcessProposal($proposalRepo, $notifier, $logger);

        $dto = new ProposalDTO(
            type: ProposalType::Suggestion,
            userId: 12345,
            userName: 'Test User',
            subject: 'Suggestion',
            content: 'Suggestion content',
        );

        // Ожидаем, что ProcessProposal выбросит RuntimeException при ошибке сохранения
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Ошибка сохранения предложения');
        $useCase->execute($dto);
    }

    /**
     * Ошибка сохранения — исходное исключение доступно через getPrevious()
     */
    public function testSaveProposalErrorExceptionHasPrevious(): void
    {
        $proposalRepo = $this->createMock(ProposalRepositoryInterface::class);
        $notifier = $this->createMock(ProposalNotifierInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $originalException = new RuntimeException('DB connection lost');
        $proposalRepo->method('save')->willThrowException($originalException);

        $notifier->expects($this->never())->method('notify');
        $logger->method('error');

        $useCase = new ProcessProposal($proposalRepo, $notifier, $logger);

        $dto = new ProposalDTO(
            type: ProposalType::Feature,
            userId: 12345,
            userName: 'Test User',
            subject: 'Feature',
            content: 'Content',
        );

        try {
            $useCase->execute($dto);
            $this->fail('Ожидалось RuntimeException');
        } catch (RuntimeException $e) {
            // Проверяем что исходное исключение доступно через getPrevious()
            $this->assertSame($originalException, $e->getPrevious());
        }
    }

    /**
     * Проверка что ProposalDTO формируется правильно — type передаётся корректно
     */
    public function testProposalDtoTypeIsPassedCorrectly(): void
    {
        $proposalRepo = $this->createMock(ProposalRepositoryInterface::class);
        $notifier = $this->createMock(ProposalNotifierInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        // Перехватываем Proposal в save() для проверки корректности данных
        $capturedProposal = null;
        $proposalRepo->expects($this->once())->method('save')
            ->willReturnCallback(function ($proposal) use (&$capturedProposal) {
                $capturedProposal = $proposal;
                return $proposal;
            });

        $logger->method('info');
        $notifier->method('notify');

        $useCase = new ProcessProposal($proposalRepo, $notifier, $logger);

        $dto = new ProposalDTO(
            type: ProposalType::Feature,
            userId: 55555,
            userName: 'DTO Tester',
            subject: 'Test Subject',
            content: 'Test Content',
        );

        $useCase->execute($dto);

        // Проверяем что Proposal сформирован из DTO корректно
        $this->assertNotNull($capturedProposal);
        $this->assertSame(ProposalType::Feature, $capturedProposal->getType());
        $this->assertSame(55555, $capturedProposal->getUserId());
        $this->assertSame('DTO Tester', $capturedProposal->getUserName());
        $this->assertSame('Test Subject', $capturedProposal->getSubject());
        $this->assertSame('Test Content', $capturedProposal->getContent());
    }

    /**
     * Проверка что ProposalDTO с Suggestion type формируется правильно
     */
    public function testProposalDtoSuggestionTypeIsPassedCorrectly(): void
    {
        $proposalRepo = $this->createMock(ProposalRepositoryInterface::class);
        $notifier = $this->createMock(ProposalNotifierInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $capturedProposal = null;
        $proposalRepo->expects($this->once())->method('save')
            ->willReturnCallback(function ($proposal) use (&$capturedProposal) {
                $capturedProposal = $proposal;
                return $proposal;
            });

        $logger->method('info');
        $notifier->method('notify');

        $useCase = new ProcessProposal($proposalRepo, $notifier, $logger);

        $dto = new ProposalDTO(
            type: ProposalType::Suggestion,
            userId: 77777,
            userName: 'Suggester',
            subject: 'Suggestion Subject',
            content: 'Suggestion Content',
        );

        $useCase->execute($dto);

        $this->assertNotNull($capturedProposal);
        $this->assertSame(ProposalType::Suggestion, $capturedProposal->getType());
        $this->assertSame(77777, $capturedProposal->getUserId());
        $this->assertSame('Suggester', $capturedProposal->getUserName());
    }

    /**
     * Успешное сохранение — логируется info с правильным type и userId
     */
    public function testSuccessfulSaveLogsInfoWithTypeAndUserId(): void
    {
        $proposalRepo = $this->createMock(ProposalRepositoryInterface::class);
        $notifier = $this->createMock(ProposalNotifierInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $proposalRepo->method('save');
        $notifier->method('notify');

        // Проверяем что лог содержит type=feature и userId=12345
        $logger->expects($this->once())->method('info')
            ->with($this->logicalAnd(
                $this->stringContains('feature'),
                $this->stringContains('12345'),
            ));

        $useCase = new ProcessProposal($proposalRepo, $notifier, $logger);

        $dto = new ProposalDTO(
            type: ProposalType::Feature,
            userId: 12345,
            userName: 'Test User',
            subject: 'New feature',
            content: 'Feature description',
        );

        $useCase->execute($dto);
    }

    /**
     * Ошибка сохранения — логируется error с сообщением исключения
     */
    public function testSaveErrorLogsErrorMessage(): void
    {
        $proposalRepo = $this->createMock(ProposalRepositoryInterface::class);
        $notifier = $this->createMock(ProposalNotifierInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $proposalRepo->method('save')->willThrowException(new RuntimeException('Connection timeout'));

        // Нотификатор НЕ вызывается при ошибке сохранения
        $notifier->expects($this->never())->method('notify');

        // Проверяем что лог содержит сообщение об ошибке
        $logger->expects($this->once())->method('error')
            ->with($this->stringContains('Connection timeout'));

        $useCase = new ProcessProposal($proposalRepo, $notifier, $logger);

        $dto = new ProposalDTO(
            type: ProposalType::Feature,
            userId: 12345,
            userName: 'Test User',
            subject: 'Feature',
            content: 'Content',
        );

        try {
            $useCase->execute($dto);
        } catch (RuntimeException) {
            // Ожидаемое исключение
        }
    }

    /**
     * Ошибка нотификатора НЕ прерывает сохранение предложения
     */
    public function testNotifierFailureDoesNotBreakSave(): void
    {
        $proposalRepo = $this->createMock(ProposalRepositoryInterface::class);
        $notifier = $this->createMock(ProposalNotifierInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        // Сохранение проходит успешно
        $proposalRepo->expects($this->once())->method('save');

        // Нотификатор выбрасывает исключение
        $notifier->method('notify')->willThrowException(new RuntimeException('SMTP connection failed'));

        // Сохранение логируется как info
        $logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('Предложение сохранено'));

        // Ошибка нотификатора логируется как error
        $logger->expects($this->once())->method('error')
            ->with($this->stringContains('SMTP connection failed'));

        $useCase = new ProcessProposal($proposalRepo, $notifier, $logger);

        $dto = new ProposalDTO(
            type: ProposalType::Feature,
            userId: 12345,
            userName: 'Test User',
            subject: 'Feature',
            content: 'Content',
        );

        // Исключение НЕ должно быть выброшено — сохранение прошло успешно
        $useCase->execute($dto);
    }
}
