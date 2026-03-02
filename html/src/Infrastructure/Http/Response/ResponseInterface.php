<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Http\Response;

interface ResponseInterface
{
    public function send(): void;

    /**
     * @return int
     */
    public function getStatusCode(): int;
}
