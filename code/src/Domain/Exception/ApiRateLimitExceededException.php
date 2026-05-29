<?php

declare(strict_types=1);

namespace MkdBot\Domain\Exception;

use RuntimeException;
use Throwable;

/**
 * Исключение — превышен лимит запросов API (429 Too Many Requests)
 * Выбрасывается когда все попытки retry исчерпаны
 */
class ApiRateLimitExceededException extends RuntimeException
{
    public function __construct(
        string $message = 'Превышен лимит запросов API',
        int $code = 429,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
