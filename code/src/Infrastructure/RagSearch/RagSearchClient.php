<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\RagSearch;

use GuzzleHttp\Exception\GuzzleException;
use MkdBot\Domain\Interface\RagSearchClientInterface;
use MkdBot\Domain\ValueObject\RagSearchResult;
use MkdBot\Domain\ValueObject\RagSearchSource;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * HTTP-клиент к Yandex Cloud Function mkd-rag-search
 *
 * Вызывает Cloud Function через HTTP POST с X-Api-Key авторизацией.
 * Cloud Function выполняет RAG-поиск через Yandex Responses API + file_search.
 */
class RagSearchClient implements RagSearchClientInterface
{
    private const MAX_QUESTION_LENGTH = 2000;

    public function __construct(
        private readonly string $functionUrl,
        private readonly string $apiKey,
        private readonly LoggerInterface $logger,
        private readonly ClientInterface $httpClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
    ) {
    }

    public function search(string $question): RagSearchResult
    {
        if ($this->functionUrl === '' || $this->apiKey === '') {
            $this->logger->error('RAG-поиск: не настроен URL функции или API-ключ');
            return RagSearchResult::error(500, 'RAG-сервис не настроен');
        }

        $trimmedQuestion = trim($question);
        if ($trimmedQuestion === '') {
            $this->logger->warning('RAG-поиск: пустой вопрос');
            return RagSearchResult::error(400, 'Вопрос не может быть пустым');
        }

        if (mb_strlen($trimmedQuestion) > self::MAX_QUESTION_LENGTH) {
            $trimmedQuestion = mb_substr($trimmedQuestion, 0, self::MAX_QUESTION_LENGTH);
            $this->logger->info('RAG-поиск: вопрос обрезан до ' . self::MAX_QUESTION_LENGTH . ' символов');
        }

        $this->logger->info("RAG-поиск: отправка запроса, длина вопроса=" . mb_strlen($trimmedQuestion));

        $payload = json_encode(['question' => $trimmedQuestion], JSON_THROW_ON_ERROR);

        try {
            $request = $this->requestFactory->createRequest('POST', $this->functionUrl)
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('X-Api-Key', $this->apiKey)
                ->withBody($this->streamFactory->createStream($payload));

            $response = $this->httpClient->sendRequest($request);
        } catch (GuzzleException $e) {
            $this->logger->error("RAG-поиск: ошибка HTTP-запроса: {$e->getMessage()}");
            throw new \RuntimeException("Ошибка связи с RAG-сервисом: {$e->getMessage()}", $e->getCode(), $e);
        }

        $httpCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();

        return $this->parseResponse($body, $httpCode);
    }

    private function parseResponse(string $body, int $httpCode): RagSearchResult
    {
        $this->logger->info("RAG-поиск: получен ответ HTTP {$httpCode}");

        $wrapper = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error("RAG-поиск: невалидный JSON в ответе: " . json_last_error_msg());
            return RagSearchResult::error(502, 'Невалидный ответ от RAG-сервиса');
        }

        $actualHttpCode = $wrapper['statusCode'] ?? $httpCode;

        $responseData = $wrapper['body'] ?? $wrapper;
        if (is_string($responseData)) {
            $responseData = json_decode($responseData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error("RAG-поиск: невалидный JSON в body: " . json_last_error_msg());
                return RagSearchResult::error(502, 'Невалидный ответ от RAG-сервиса');
            }
        }

        if ($actualHttpCode >= 400 || ($responseData['success'] ?? true) === false) {
            $errorCode = $responseData['error']['code'] ?? $actualHttpCode;
            $errorMessage = $responseData['error']['message'] ?? 'Неизвестная ошибка RAG-сервиса';

            $this->logger->warning("RAG-поиск: ошибка сервиса [{$errorCode}]: {$errorMessage}");

            // Клиентские ошибки (400, 401) — без retry
            if ($actualHttpCode < 500 && $actualHttpCode >= 400) {
                return RagSearchResult::error($errorCode, $errorMessage);
            }

            // Серверные ошибки (500, 502) — пробрасываем как RuntimeException для retry
            throw new \RuntimeException("Ошибка RAG-сервиса [{$errorCode}]: {$errorMessage}", $errorCode);
        }

        $answer = $responseData['answer'] ?? '';
        $sources = [];

        foreach ($responseData['sources'] ?? [] as $sourceData) {
            $sources[] = RagSearchSource::fromArray($sourceData);
        }

        $this->logger->info("RAG-поиск: получен ответ, длина=" . mb_strlen($answer) . ", источников=" . count($sources));

        return RagSearchResult::success($answer, $sources);
    }
}
