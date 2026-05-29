<?php

declare(strict_types=1);

class Config
{
    public readonly string $folderId;
    public readonly string $iamToken;

    /** @var string[] */
    public readonly array $vectorStoreIds;
    public readonly string $modelUri;
    public readonly int $timeout;

    /**
     * @param string[] $vectorStoreIds
     */
    private function __construct(
        string $folderId,
        string $iamToken,
        array $vectorStoreIds,
        string $modelUri,
        int $timeout
    ) {
        $this->folderId = $folderId;
        $this->iamToken = $iamToken;
        $this->vectorStoreIds = $vectorStoreIds;
        $this->modelUri = $modelUri;
        $this->timeout = $timeout;
    }

    public static function fromEnvironment(): self
    {
        $folderId = getenv('YANDEX_FOLDER_ID') ?: '';
        $vectorStoreIdsStr = getenv('VECTOR_STORE_IDS') ?: '';
        $vectorStoreIds = array_filter(array_map('trim', explode(',', $vectorStoreIdsStr)));
        $modelUri = getenv('YANDEX_MODEL_URI') ?: "gpt://{$folderId}/yandexgpt-lite/latest";
        $timeout = (int)(getenv('SEARCH_TIMEOUT') ?: 30);

        // Получение IAM-токена: сначала через metadata service (когда SA привязан к функции),
        // затем через переменную окружения YANDEX_IAM_TOKEN
        $iamToken = self::fetchIamTokenFromMetadata();
        if ($iamToken === '') {
            $iamToken = getenv('YANDEX_IAM_TOKEN') ?: '';
        }

        return new self($folderId, $iamToken, $vectorStoreIds, $modelUri, $timeout);
    }

    /**
     * Получение IAM-токена через metadata service Yandex Cloud.
     *
     * Когда сервисный аккаунт привязан к Cloud Function, metadata service
     * автоматически предоставляет IAM-токен.
     *
     * @see https://yandex.cloud/docs/docs/compute/operations/vm-connect/auth-inside-vm
     */
    private static function fetchIamTokenFromMetadata(): string
    {
        $url = 'http://169.254.169.254/computeMetadata/v1/instance/service-accounts/default/token';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_HTTPHEADER => [
                'Metadata-Flavor: Google',
            ],
        ]);

        $body = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false || $httpCode !== 200) {
            return '';
        }

        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return '';
        }

        return $data['access_token'] ?? '';
    }

    public function isValid(): bool
    {
        return $this->folderId !== ''
            && $this->iamToken !== ''
            && count($this->vectorStoreIds) > 0;
    }

    /**
     * @return string[]
     */
    public function getMissingConfig(): array
    {
        $missing = [];
        if ($this->folderId === '') {
            $missing[] = 'YANDEX_FOLDER_ID';
        }
        if ($this->iamToken === '') {
            $missing[] = 'IAM-токен (metadata service или YANDEX_IAM_TOKEN)';
        }
        if (count($this->vectorStoreIds) === 0) {
            $missing[] = 'VECTOR_STORE_IDS';
        }
        return $missing;
    }
}
