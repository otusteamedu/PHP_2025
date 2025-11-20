<?php

declare(strict_types=1);

use Otus\Kernel\ApplicationFactory;

require __DIR__ . '/../vendor/autoload.php';

echo ApplicationFactory::factory()->run();
