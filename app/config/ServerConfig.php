<?php

declare(strict_types=1);

namespace App\Config;

/**
 * Неизменяемый объект конфигурации одного устройства Hikvision
 */
final class ServerConfig
{
    public function __construct(
        public readonly string $host,
        public readonly string $login,
        public readonly string $password,
        public readonly string $name = 'unknown'  // для удобства: 'in', 'out', 'test'
    ) {}

    public function getBaseUrl(): string
    {
        return "https://{$this->host}";
    }

    public function getAuth(): string
    {
        return "{$this->login}:{$this->password}";
    }

    public function getFullEventUrl(): string
    {
        return $this->getBaseUrl() . "/ISAPI/AccessControl/AcsEvent?format=json";
    }

    public function getFullUserUrl(): string
    {
        return $this->getBaseUrl() . "/ISAPI/AccessControl/UserInfo/Search?format=json";
    }
}