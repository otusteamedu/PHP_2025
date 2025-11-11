<?php

namespace App;

class EventConfig
{
    private int $id;
    private string $config;

    public function __construct(
        int $id,
        string $config,
    ) {
        $this->id = $id;
        $this->config = $config;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getConfig(): string
    {
        return $this->config;
    }
}
