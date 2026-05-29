<?php

declare(strict_types=1);

class SearchService
{
    /**
     * Эндпоинт OpenAI-compatible chat completions для YandexGPT с File Search Tool.
     * Поддерживает инструмент file_search с vector_store_ids.
     *
     * @see https://aistudio.yandex.ru/docs/ru/ai-studio/concepts/agents/tools/filesearch.html
     */
    private const API_URL = 'https://llm.api.cloud.yandex.net/v1/chat/completions'; // responses!

    private const SYSTEM_INSTRUCTION = <<<'TEXT'
Ты — помощник по вопросам ЖКХ (жилищно-коммунального хозяйства) для жителей многоквартирных домов.

Отвечай на вопросы жителей на основе предоставленных документов.
Если ответ не найден в документах, честно скажи, что информации нет.
Отвечай кратко и по существу. Указывай ссылки на источники, если они доступны.
TEXT;

    public function __construct(
        private readonly Config $config
    ) {}

    public function search(string $question, ?int $chatId = null): SearchResponse
    {
        if (!$this->config->isValid()) {
            $missing = $this->config->getMissingConfig();
            return SearchResponse::error(
                500,
                'Неполная конфигурация: ' . implode(', ', $missing)
            );
        }

        if (trim($question) === '') {
            return SearchResponse::error(400, 'Вопрос не может быть пустым');
        }

        $payload = $this->buildPayload($question);
        $response = $this->sendRequest($payload);

        if ($response === null) {
            return SearchResponse::error(502, 'Не удалось подключиться к Yandex API');
        }

        return $this->parseResponse($response);
    }

    /**
     * Формирование тела запроса для OpenAI-compatible chat completions API
     * с инструментом file_search.
     *
     * Формат: OpenAI-compatible chat completions с поддержкой tools.
     * Инструмент file_search включает гибридный поиск по индексам Vector Store.
     */
    private function buildPayload(string $question): array
    {
        return [
            'model' => $this->config->modelUri,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => self::SYSTEM_INSTRUCTION,
                ],
                [
                    'role' => 'user',
                    'content' => $question,
                ],
            ],
            'tools' => [
                [
                    'type' => 'file_search',
                    'vector_store_ids' => $this->config->vectorStoreIds,
                    'max_num_results' => 5,
                ],
            ],
        ];
    }

    /**
     * Отправка HTTP POST-запроса к Yandex API.
     *
     * @return array|null Декодированный JSON-ответ или null при ошибке соединения
     */
    private function sendRequest(array $payload): ?array
    {
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $ch = curl_init(self::API_URL);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->config->iamToken,
                'x-folder-id: ' . $this->config->folderId,
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

        if ($curlError !== '') {
            error_log("RAG-поиск: ошибка curl — $curlError");
            return null;
        }

        if ($body === false) {
            error_log('RAG-поиск: curl_exec вернул false');
            return null;
        }

        if ($httpCode >= 400) {
            $decoded = json_decode($body, true);
            $message = $decoded['error']['message']
                ?? $decoded['message']
                ?? "HTTP $httpCode";
            error_log("RAG-поиск: ошибка API — HTTP $httpCode — $message");
            return ['error' => true, 'code' => $httpCode, 'message' => $message];
        }

        $decoded = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('RAG-поиск: невалидный JSON в ответе — ' . json_last_error_msg());
            return ['error' => true, 'code' => 502, 'message' => 'Невалидный JSON в ответе от Yandex API'];
        }

        return $decoded;
    }

    /**
     * Разбор ответа OpenAI-compatible chat completions API.
     *
     * Формат ответа:
     * {
     *     "choices": [{
     *         "message": {
     *             "role": "assistant",
     *             "content": "текст ответа",
     *             "annotations": [{ "filename": "...", "file_id": "...", "type": "file_citation" }]
     *         }
     *     }]
     * }
     *
     * Также обрабатывает устаревший формат Completion API:
     * result.alternatives[0].message.text
     */
    private function parseResponse(array $response): SearchResponse
    {
        if (isset($response['error']) && $response['error'] === true) {
            $code = $response['code'] ?? 500;
            $message = $response['message'] ?? 'Неизвестная ошибка';
            return SearchResponse::error($code, $message);
        }

        // Формат OpenAI-compatible: choices[0].message.content
        $answer = '';
        $sources = [];

        $choices = $response['choices'] ?? [];
        if (is_array($choices) && count($choices) > 0) {
            $message = $choices[0]['message'] ?? [];
            $answer = $message['content'] ?? '';

            // Извлечение аннотаций (источников) из сообщения
            $annotations = $message['annotations'] ?? [];
            foreach ($annotations as $annotation) {
                $source = $this->extractSourceFromAnnotation($annotation);
                if ($source !== null) {
                    $sources[] = $source;
                }
            }
        }

        // Резервный формат: Completion API — result.alternatives[0].message.text
        if ($answer === '') {
            $answer = $response['result']['alternatives'][0]['message']['text']
                ?? $response['answer']
                ?? $response['result']['text']
                ?? '';
        }

        // Резервный формат: извлечение аннотаций из Completion API
        if (count($sources) === 0) {
            $annotations = $response['result']['alternatives'][0]['message']['annotations']
                ?? $response['annotations']
                ?? [];
            foreach ($annotations as $annotation) {
                $source = $this->extractSourceFromAnnotation($annotation);
                if ($source !== null) {
                    $sources[] = $source;
                }
            }
        }

        return SearchResponse::success($answer, $sources);
    }

    /**
     * Извлечение информации об источнике из объекта аннотации.
     *
     * Поддерживает формат Responses API (file_citation с filename)
     * и устаревший формат (file с filename).
     */
    private function extractSourceFromAnnotation(array $annotation): ?array
    {
        // Формат Responses API: { type: "file_citation", filename: "...", file_id: "..." }
        if (isset($annotation['filename'])) {
            return [
                'filename' => $annotation['filename'],
                'file_id' => $annotation['file_id'] ?? null,
            ];
        }

        // Устаревший формат: { file: { filename: "...", score: ... } }
        if (isset($annotation['file']['filename'])) {
            return [
                'filename' => $annotation['file']['filename'],
                'score' => $annotation['file']['score'] ?? null,
            ];
        }

        return null;
    }
}
