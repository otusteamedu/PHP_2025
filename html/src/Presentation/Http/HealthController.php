<?php

declare(strict_types=1);

namespace Otus\Queue\Presentation\Http;

use Otus\Queue\Application\UseCase\Health\Health;
use Otus\Queue\Infrastructure\Http\Response\ResponseInterface;
use Otus\Queue\Infrastructure\Http\ResponseFactory;

final readonly class HealthController
{
    /**
     * @param Health $health
     */
    public function __construct(
        private Health $health,
    ) {
    }

    /**
     * @return ResponseInterface
     */
    public function __invoke(): ResponseInterface
    {
        if (!$this->health->handle()) {
            return ResponseFactory::toJson([], 500);
        }

        return ResponseFactory::toJson([]);
    }
}
