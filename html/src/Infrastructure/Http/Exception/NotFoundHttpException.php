<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Http\Exception;

class NotFoundHttpException extends AbstractHttpException
{
    public function __construct()
    {
        parent::__construct('Not Found.');
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 404;
    }
}
