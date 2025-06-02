<?php

declare(strict_types=1);

namespace App\Infrastructure\Rabbit;

/**
 * Class Config
 * @package App\Infrastructure\Rabbit
 */
class Config
{
    /**
     * @var string
     */
    private string $host;
    /**
     * @var string
     */
    private string $port;
    /**
     * @var string
     */
    private string $user;
    /**
     * @var string
     */
    private string $password;
    /**
     * @var string
     */
    private string $queueName;

    /**
     *
     */
    public function __construct()
    {
        $this->host = getenv('RABBIT_HOST');
        $this->port = getenv('RABBIT_PORT');
        $this->user = getenv('RABBIT_USER');
        $this->password = getenv('RABBIT_PASSWORD');
        $this->queueName = getenv('RABBIT_QUEUE_NAME');
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getPort(): string
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getQueueName(): string
    {
        return $this->queueName;
    }
}
