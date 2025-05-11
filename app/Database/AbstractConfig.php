<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Database;

use RuntimeException;

abstract class AbstractConfig
{
    private array $config = [];
    private string $host;
    private int $port;
    private string $login;
    private string $password;
    private string $database;

    protected function setConfig(string $filename): void
    {
        $filename = $_SERVER['DOCUMENT_ROOT'] . $filename;
        if (file_exists($filename)) {
            $this->config = require($filename);

            $this->guaranteeParams();

            $this->host = $this->config['HOST'];
            $this->port = (int)$this->config['PORT'];
            $this->login = $this->config['LOGIN'];
            $this->password = $this->config['PASSWORD'];
            $this->database = $this->config['DATABASE'];
        }
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    private function guaranteeParams(): void
    {
        $expectedParams = [
            'HOST',
            'PORT',
            'LOGIN',
            'PASSWORD',
            'DATABASE',
        ];

        foreach ($expectedParams as $param) {
            if (!isset($this->config[$param])) {
                throw new RuntimeException("Param '$param' not set");
            }
        }
    }
}