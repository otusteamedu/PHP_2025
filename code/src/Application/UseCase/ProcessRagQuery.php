<?php

declare(strict_types=1);

namespace MkdBot\Application\UseCase;

use MkdBot\Application\Service\MainMenuSender;
use MkdBot\Application\Service\RagResponseFormatter;
use MkdBot\Domain\Interface\MaxBotClientInterface;
use MkdBot\Domain\Interface\RagSearchClientInterface;
use MkdBot\Domain\ValueObject\RagSearchResult;
use Psr\Log\LoggerInterface;
use RuntimeException;

class ProcessRagQuery
{
    public function __construct(
        private readonly RagSearchClientInterface $ragSearchClient,
        private readonly MaxBotClientInterface $maxBot,
        private readonly RagResponseFormatter $formatter,
        private readonly MainMenuSender $mainMenuSender,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws RuntimeException при ошибке связи с RAG-сервисом (для retry через RabbitMQ DLX)
     */
    public function execute(int $userId, string $question): void
    {
        $this->logger->info("RAG-запрос: userId={$userId}, вопрос='{$question}'");

        try {
            $result = $this->ragSearchClient->search($question);
        } catch (RuntimeException $e) {
            $this->logger->error("RAG-запрос: ошибка сервиса для userId={$userId}: " . $e->getMessage());
            $this->maxBot->sendMessageToUser(
                $userId,
                '⚠️ Не удалось получить ответ от ИИ-сервиса. Попробуйте позже.',
            );
            $this->mainMenuSender->send($userId);
            // Пробрасываем исключение для DLX-механизма RabbitMQ
            throw $e;
        }

        $formattedText = $this->formatter->format($result);
        $this->maxBot->sendMessageToUser($userId, $formattedText);
        $this->mainMenuSender->send($userId);

        $this->logger->info("RAG-запрос: ответ отправлен userId={$userId}, success=" . ($result->isSuccess() ? 'true' : 'false'));
    }
}
