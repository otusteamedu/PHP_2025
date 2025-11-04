<?php

declare(strict_types=1);

namespace App\Application\Consumer;

/**
 * Interface ConsumerInterface
 * @package App\Application\Consumer
 */
interface ConsumerInterface
{
    /**
     * @param callable $callback
     * @return void
     */
    public function consume(callable $callback): void;
}
