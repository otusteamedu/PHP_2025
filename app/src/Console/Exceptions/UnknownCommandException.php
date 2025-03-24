<?php declare(strict_types=1);

namespace App\Console\Exceptions;

use Throwable;

/**
 * Class UnknownCommandException
 * @package App\Console\Exceptions
 */
class UnknownCommandException extends Exception
{
    /**
     * @param string $route the route of the command that could not be found.
     * @param int $code the Exception code.
     * @param Throwable|null $previous the previous exception used for the exception chaining.
     */
    public function __construct(string $route, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Unknown command \"$route\".", $code, $previous);
    }
}
