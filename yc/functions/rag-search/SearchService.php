<?php

declare(strict_types=1);

class SearchApiException extends \RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $statusCode = 502,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }
}

class SearchService
{
    public function __construct(
        private readonly Config $config
    ) {}

    public function search(string $question): SearchResponse
    {
        if (!$this->config->isValid()) {
            $missing = $this->config->getMissingConfig();
            return SearchResponse::error(
                500,
                'Неполная конфигурация: ' . implode(', ', $missing)
            );
        }

        $payload = $this->buildPayload($question);
        $response = $this->sendRequest($payload);

        return $this->parseResponse($response);
    }

    /** @see https://aistudio.yandex.ru/docs/ru/ai-studio/concepts/agents/tools/filesearch.html */
    private function buildPayload(string $question): array
    {
        return [
            'model' => $this->config->modelUri,
            'instructions' => $this->config->instructions,
            'tools' => [
                [
                    'type' => 'file_search',
                    'vector_store_ids' => $this->config->vectorStoreIds,
                    'max_num_results' => 5,
                ],
            ],
            'input' => $question,
        ];
    }

    /** @throws SearchApiException */
    private function sendRequest(array $payload): array
    {
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $ch = curl_init(Config::API_URL);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->config->authToken(),
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->config->timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $body = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($body === false || $curlError !== '') {
            error_log("RAG-поиск: ошибка curl — $curlError");
            throw new SearchApiException('Не удалось подключиться к Yandex API', 502);
        }

        if ($httpCode >= 400) {
            $decoded = json_decode($body, true);
            $message = $decoded['error']['message']
                ?? $decoded['message']
                ?? "HTTP $httpCode";
            error_log("RAG-поиск: ошибка API — HTTP $httpCode — $message");
            throw new SearchApiException("Ошибка Yandex API: $message", $httpCode >= 500 ? 502 : 400);
        }

        $decoded = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('RAG-поиск: невалидный JSON в ответе — ' . json_last_error_msg());
            throw new SearchApiException('Невалидный JSON в ответе от Yandex API', 502);
        }

        return $decoded;
    }

    private function parseResponse(array $response): SearchResponse
    {
        if (!isset($response['output']) || !is_array($response['output'])) {
            return SearchResponse::error(502, 'Пустой или некорректный ответ от Yandex API');
        }

        $answer = '';
        $sources = [];

        foreach ($response['output'] as $item) {
            $itemType = $item['type'] ?? '';

            if ($itemType === 'file_search_call') {
                foreach ($item['results'] ?? [] as $result) {
                    $sources[] = SearchSource::fromFileSearchResult($result);
                }
            }

            if ($itemType === 'message') {
                foreach ($item['content'] ?? [] as $contentBlock) {
                    if (($contentBlock['type'] ?? '') === 'output_text') {
                        $answer = $contentBlock['text'] ?? '';

                        foreach ($contentBlock['annotations'] ?? [] as $annotation) {
                            if (($annotation['type'] ?? '') === 'file_citation') {
                                $sources[] = SearchSource::fromFileCitation($annotation);
                            }
                        }
                    }
                }
            }
        }

        return SearchResponse::success($answer, $sources);
    }
}
