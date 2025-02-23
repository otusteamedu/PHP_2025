<?php declare(strict_types=1);

namespace App\Http\Exceptions;

use Throwable;

/**
 * Class ServerErrorHttpException
 * @package App\Http\Exceptions
 */
class ServerErrorHttpException extends HttpException
{
    /**
     * @param string|null $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(?string $message = null, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(500, $message, $code, $previous);
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'Internal Server Error';
    }
}
