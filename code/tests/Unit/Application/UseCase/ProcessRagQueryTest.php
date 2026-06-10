<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Application\UseCase;

use MkdBot\Application\Service\MainMenuSender;
use MkdBot\Application\Service\RagResponseFormatter;
use MkdBot\Application\UseCase\ProcessRagQuery;
use MkdBot\Domain\Interface\MaxBotClientInterface;
use MkdBot\Domain\Interface\RagSearchClientInterface;
use MkdBot\Domain\ValueObject\RagSearchResult;
use MkdBot\Domain\ValueObject\RagSearchSource;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;

class ProcessRagQueryTest extends TestCase
{
    private RagSearchClientInterface $ragSearchClient;
    private MaxBotClientInterface $maxBot;
    private RagResponseFormatter $formatter;
    private MainMenuSender $mainMenuSender;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->ragSearchClient = $this->createMock(RagSearchClientInterface::class);
        $this->maxBot = $this->createMock(MaxBotClientInterface::class);
        $this->formatter = new RagResponseFormatter();
        $this->mainMenuSender = $this->createMock(MainMenuSender::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    private function createUseCase(): ProcessRagQuery
    {
        return new ProcessRagQuery(
            $this->ragSearchClient,
            $this->maxBot,
            $this->formatter,
            $this->mainMenuSender,
            $this->logger,
        );
    }

    // --- Успешные сценарии ---

    public function testExecuteWithSuccessfulResultSendsAnswerAndMenu(): void
    {
        $result = RagSearchResult::success('Оплата производится через банк');

        $this->ragSearchClient->method('search')->willReturn($result);
        $this->maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, $this->stringContains('Оплата производится через банк'));
        $this->mainMenuSender->expects($this->once())->method('send')->with(123);

        $useCase = $this->createUseCase();
        $useCase->execute(123, 'Как оплатить ЖКХ?');
    }

    public function testExecuteWithSourcesSendsSourcesInMessage(): void
    {
        $sources = [new RagSearchSource('faq.md', 'file-1', 0.9, 'Текст')];
        $result = RagSearchResult::success('Ответ', $sources);

        $this->ragSearchClient->method('search')->willReturn($result);
        $this->maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, $this->stringContains('Источники'));
        $this->mainMenuSender->expects($this->once())->method('send');

        $useCase = $this->createUseCase();
        $useCase->execute(123, 'Вопрос');
    }

    public function testExecuteWithEmptyAnswerSendsNotFoundMessage(): void
    {
        $result = RagSearchResult::success('');

        $this->ragSearchClient->method('search')->willReturn($result);
        $this->maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, $this->stringContains('не удалось найти ответ'));
        $this->mainMenuSender->expects($this->once())->method('send');

        $useCase = $this->createUseCase();
        $useCase->execute(123, 'Непонятный вопрос');
    }

    // --- Ошибки RAG-клиента ---

    public function testExecuteWithClientErrorSendsErrorMessageAndMenu(): void
    {
        $result = RagSearchResult::error(400, 'Вопрос обязателен');

        $this->ragSearchClient->method('search')->willReturn($result);
        $this->maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, $this->stringContains('Не удалось обработать вопрос'));
        $this->mainMenuSender->expects($this->once())->method('send');

        $useCase = $this->createUseCase();
        $useCase->execute(123, '');
    }

    public function testExecuteWithAuthErrorSendsServiceUnavailableMessage(): void
    {
        $result = RagSearchResult::error(401, 'Неверный API-ключ');

        $this->ragSearchClient->method('search')->willReturn($result);
        $this->maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, $this->stringContains('Сервис временно недоступен'));
        $this->mainMenuSender->expects($this->once())->method('send');

        $useCase = $this->createUseCase();
        $useCase->execute(123, 'Вопрос');
    }

    // --- Исключения из RAG-клиента (серверные ошибки) ---

    public function testExecuteWithRuntimeExceptionSendsErrorMessageAndThrows(): void
    {
        $exception = new RuntimeException('Ошибка связи с RAG-сервисом: timeout', 28);

        $this->ragSearchClient->method('search')->willThrowException($exception);
        $this->maxBot->expects($this->once())->method('sendMessageToUser')
            ->with(123, $this->stringContains('Не удалось получить ответ'));
        $this->mainMenuSender->expects($this->once())->method('send');

        $useCase = $this->createUseCase();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Ошибка связи с RAG-сервисом: timeout');

        $useCase->execute(123, 'Вопрос');
    }

    public function testExecuteWithServerExceptionSendsErrorMessageAndThrows(): void
    {
        $exception = new RuntimeException('Ошибка RAG-сервиса [502]: Сервис недоступен', 502);

        $this->ragSearchClient->method('search')->willThrowException($exception);
        $this->maxBot->expects($this->once())->method('sendMessageToUser');
        $this->mainMenuSender->expects($this->once())->method('send');

        $useCase = $this->createUseCase();
        $this->expectException(RuntimeException::class);

        $useCase->execute(123, 'Вопрос');
    }

    // --- Логирование ---

    public function testExecuteLogsInfoOnSuccess(): void
    {
        $result = RagSearchResult::success('Ответ');

        $this->ragSearchClient->method('search')->willReturn($result);
        $this->logger->expects($this->exactly(2))->method('info');

        $useCase = $this->createUseCase();
        $useCase->execute(123, 'Вопрос');
    }

    public function testExecuteLogsErrorOnRuntimeException(): void
    {
        $exception = new RuntimeException('timeout', 28);

        $this->ragSearchClient->method('search')->willThrowException($exception);
        $this->logger->expects($this->once())->method('error');

        $useCase = $this->createUseCase();
        try {
            $useCase->execute(123, 'Вопрос');
        } catch (RuntimeException) {
            // Ожидаемое исключение
        }
    }
}
