<?php

declare(strict_types=1);

namespace Otus\Food\Application\Strategy;

use Exception;

class HarvesterException extends Exception
{
    /**
     * @param string $title
     */
    public function __construct(string $title)
    {
        $message = sprintf(
            'We don\'t cook `%s`',
            $title,
        );

        parent::__construct($message);
    }
}
