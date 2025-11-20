<?php

declare(strict_types=1);

use Otus\Kernel\Application;
use Otus\Kernel\Http\Request;
use Otus\Kernel\ValueObject;

require __DIR__ . '/../vendor/autoload.php';

echo new Application(
    new Request(
        new ValueObject($_GET),
        new ValueObject($_POST),
        new ValueObject($_SESSION),
        new ValueObject($_SERVER),
    )
)->run();
