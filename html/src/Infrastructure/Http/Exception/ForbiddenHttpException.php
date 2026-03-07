<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Http\Exception;

class ForbiddenHttpException extends AbstractHttpException
{
    public function __construct()
    {
        parent::__construct('Forbidden.');
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 403;
    }
}
