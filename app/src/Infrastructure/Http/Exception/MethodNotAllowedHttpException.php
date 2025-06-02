<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Exception;

use Throwable;

/**
 * Class MethodNotAllowedHttpException
 * @package App\Infrastructure\Http\Exception
 */
class MethodNotAllowedHttpException extends HttpException
{
    /**
     * @param string|null $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(?string $message = null, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(405, $message, $code, $previous);
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'Method Not Allowed';
    }
}
