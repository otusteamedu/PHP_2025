<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Exception;

use Exception;
use Throwable;

/**
 * Class HttpException
 * @package App\Infrastructure\Http\Exception
 */
class HttpException extends Exception
{
    /**
     * @var int
     */
    private int $statusCode;

    /**
     * @param int $status
     * @param string|null $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(int $status, ?string $message = null, int $code = 0, ?Throwable $previous = null)
    {
        $this->statusCode = $status;
        parent::__construct((string)$message, $code, $previous);
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'Error';
    }
}
