<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Http\Exception;

use Throwable;

class ServerInternalErrorHttpException extends AbstractHttpException
{
    /**
     * @param Throwable $throwable
     */
    public function __construct(Throwable $throwable)
    {
        parent::__construct($throwable->getMessage());
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 500;
    }
}
