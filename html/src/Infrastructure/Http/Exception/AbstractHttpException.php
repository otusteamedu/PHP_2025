<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Http\Exception;

use Exception;

abstract class AbstractHttpException extends Exception
{
    /**
     * @return int
     */
    abstract public function getStatusCode(): int;
}
