<?php

declare(strict_types=1);

namespace Otus\Food\Infrastructure\Kernel\Console;

use Exception;

class UnresolveHandlerException extends Exception
{
    /**
     * @param string $command
     */
    public function __construct(string $command)
    {
        $message = sprintf(
            'Unresolvable handler `%s`',
            $command,
        );

        parent::__construct($message);
    }
}
