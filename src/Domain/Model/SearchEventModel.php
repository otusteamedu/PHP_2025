<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Infrastructure\NoSQL\Repository\EventRepositoryInterface;

class SearchEventModel
{
    private readonly array $params;

    public function __construct(array $params)
    {
        uksort($params, "strnatcmp");
        $this->params = $params;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getPreparedParams(): array
    {
        $params = [];
        foreach ($this->getParams() as $key => $value) {
            $params[] = "$key=$value";
        }
        $params[] = implode('&', array_values($params));

        return $params;
    }

    public function getEventKeyPattern(float $score): string
    {
        return EventRepositoryInterface::EVENTS_KEY_PREFIX . ':*' . $score;
    }
}
