<?php

declare(strict_types=1);

class Config
{
    public const API_URL = 'https://ai.api.cloud.yandex.net/v1/responses';

    public readonly string $folderId;
    public readonly string $yandexApiKey;

    /** @var string[] */
    public readonly array $vectorStoreIds;
    public readonly string $modelUri;
    public readonly int $timeout;
    public readonly string $instructions;
    public readonly string $webhookApiKey;

    /** API-ключ AI Studio (передаётся через Lockbox → env YANDEX_API_KEY) */
    public function authToken(): string
    {
        return $this->yandexApiKey;
    }

    /**
     * @param string[] $vectorStoreIds
     */
    private function __construct(
        string $folderId,
        string $yandexApiKey,
        array $vectorStoreIds,
        string $modelUri,
        int $timeout,
        string $instructions,
        string $webhookApiKey
    ) {
        $this->folderId = $folderId;
        $this->yandexApiKey = $yandexApiKey;
        $this->vectorStoreIds = $vectorStoreIds;
        $this->modelUri = $modelUri;
        $this->timeout = $timeout;
        $this->instructions = $instructions;
        $this->webhookApiKey = $webhookApiKey;
    }

    public static function fromEnvironment(): self
    {
        $folderId = getenv('YANDEX_FOLDER_ID') ?: '';
        $vectorStoreIdsStr = getenv('VECTOR_STORE_IDS') ?: '';
        $vectorStoreIds = array_filter(array_map('trim', explode(',', $vectorStoreIdsStr)));
        $modelUri = getenv('YANDEX_MODEL_URI') ?: "gpt://{$folderId}/yandexgpt-lite";
        $timeout = (int)(getenv('SEARCH_TIMEOUT') ?: 30);
        $instructions = getenv('YANDEX_INSTRUCTIONS') ?: 'Ты — умный ассистент для жителей многоквартирного дома. Отвечай на вопросы по ЖКХ, используя информацию из подключённых поисковых индексов. Если ответа нет в документах — честно скажи об этом.';
        $webhookApiKey = getenv('WEBHOOK_API_KEY') ?: '';
        $yandexApiKey = getenv('YANDEX_API_KEY') ?: '';

        return new self($folderId, $yandexApiKey, $vectorStoreIds, $modelUri, $timeout, $instructions, $webhookApiKey);
    }

    public function isValid(): bool
    {
        return $this->folderId !== ''
            && $this->yandexApiKey !== ''
            && count($this->vectorStoreIds) > 0;
    }

    /** @return string[] */
    public function getMissingConfig(): array
    {
        $missing = [];
        if ($this->folderId === '') {
            $missing[] = 'YANDEX_FOLDER_ID';
        }
        if ($this->yandexApiKey === '') {
            $missing[] = 'YANDEX_API_KEY';
        }
        if (count($this->vectorStoreIds) === 0) {
            $missing[] = 'VECTOR_STORE_IDS';
        }
        return $missing;
    }
}
