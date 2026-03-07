<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Queue\Adapter;

use Otus\Queue\Infrastructure\Queue\Payload;

interface AdapterInterface
{
    /**
     * @param Payload $payload
     */
    public function push(Payload $payload): void;

    /**
     * @param Payload $payload
     */
    public function pull(Payload $payload): void;
}
