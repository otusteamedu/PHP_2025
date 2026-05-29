<?php

declare(strict_types=1);

namespace MkdBot\Domain\Interface;

use SensitiveParameterValue;

/**
 * Интерфейс конфигурации Max Bot API — независимая абстракция от библиотеки
 * НЕ расширяет MaxApiConfigInterface — полная абстракция от final-класса MaxApiConfig
 */
interface MaxBotConfigInterface
{
    public function getAccessToken(): ?SensitiveParameterValue;
    public function getBaseUrl(): string;
    public function getHttpClient(): ?object;
    public function getMaxHttpClient(): ?object;
    public function getRetryAttempts(): array;
    public function getTimeout(): int;
    public function getConnectTimeout(): int;

    public function setAccessToken(?string $token): self;
    public function setBaseUrl(string $url): self;
    public function setHttpClient(?object $client): self;
    public function setMaxHttpClient(?object $client): self;
    public function setRetryAttempts(array $attempts): self;
    public function setTimeout(int $timeout): self;
    public function setConnectTimeout(int $timeout): self;
}
