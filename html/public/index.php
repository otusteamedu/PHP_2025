<?php

declare(strict_types=1);

use Otus\Kernel\Application;

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../config/env.php';
require __DIR__ . '/../config/container.php';
require __DIR__ . '/../config/routes.php';

Application::getInstance()->handle();
