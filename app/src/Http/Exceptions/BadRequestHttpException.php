<?php declare(strict_types=1);

namespace App\Http\Exceptions;

use Throwable;

/**
 * Class BadRequestHttpException
 * @package App\Http\Exceptions
 */
class BadRequestHttpException extends HttpException
{
    /**
     * @param string|null $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(?string $message = null, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(400, $message, $code, $previous);
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'Bad Request';
    }
}
