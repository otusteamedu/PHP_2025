<?php

declare(strict_types=1);

namespace Otus\Kernel;

use Otus\Kernel\Http\Request;

readonly class ApplicationFactory
{
    /**
     * @return Application
     */
    public static function factory(): Application
    {
        return new Application(
            new Request(
                new ValueObject($_GET),
                new ValueObject($_POST),
                new ValueObject($_SESSION),
                new ValueObject($_SERVER),
            )
        );
    }
}
