<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\MaxBot;

use MaxMessenger\Bot\MaxApiConfig;
use MkdBot\Domain\Interface\MaxBotConfigInterface;
use SensitiveParameterValue;

/**
 * Конфигурация Max Bot API — делегирует final-класс MaxApiConfig через композицию
 * НЕ расширяет MaxApiConfigInterface из библиотеки — независимая абстракция
 */
class MaxBotConfig implements MaxBotConfigInterface
{
    private MaxApiConfig $innerConfig;

    public function __construct(?string $accessToken = null)
    {
        $this->innerConfig = new MaxApiConfig($accessToken);
    }

    public function getAccessToken(): ?SensitiveParameterValue
    {
        return $this->innerConfig->getAccessToken();
    }

    public function getBaseUrl(): string
    {
        return $this->innerConfig->getBaseUrl();
    }

    public function getHttpClient(): ?object
    {
        return $this->innerConfig->getHttpClient();
    }

    public function getMaxHttpClient(): ?object
    {
        return $this->innerConfig->getMaxHttpClient();
    }

    public function getRetryAttempts(): array
    {
        return $this->innerConfig->getRetryAttempts();
    }

    public function getTimeout(): int
    {
        return $this->innerConfig->getTimeout();
    }

    public function getConnectTimeout(): int
    {
        return $this->innerConfig->getConnectTimeout();
    }

    public function setAccessToken(?string $token): self
    {
        $this->innerConfig->setAccessToken($token);
        return $this;
    }

    public function setBaseUrl(string $url): self
    {
        $this->innerConfig->setBaseUrl($url);
        return $this;
    }

    public function setHttpClient(?object $client): self
    {
        // MaxApiConfig ожидает Mj4444\SimpleHttpClient\Contracts\HttpClientInterface
        // Делегируем только если клиент совместим, иначе пропускаем
        if ($client instanceof \Mj4444\SimpleHttpClient\Contracts\HttpClientInterface || $client === null) {
            $this->innerConfig->setHttpClient($client);
        }

        return $this;
    }

    public function setMaxHttpClient(?object $client): self
    {
        // MaxApiConfig не имеет setMaxHttpClient — оставлено для будущего использования
        return $this;
    }

    public function setRetryAttempts(array $attempts): self
    {
        $this->innerConfig->setRetryAttempts($attempts);
        return $this;
    }

    public function setTimeout(int $timeout): self
    {
        $this->innerConfig->setTimeout($timeout);
        return $this;
    }

    public function setConnectTimeout(int $timeout): self
    {
        $this->innerConfig->setConnectTimeout($timeout);
        return $this;
    }

    /**
     * Возвращает внутренний MaxApiConfig для совместимости с MaxApiClient
     */
    public function getInnerConfig(): MaxApiConfig
    {
        return $this->innerConfig;
    }
}
