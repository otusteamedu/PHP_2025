<?php

declare(strict_types=1);

use Pryaniki\App\Presentation\Console\ConsoleEmailValidatorRunner;

require '../app/vendor/autoload.php';

$app = new ConsoleEmailValidatorRunner();

$result = $app->run();
echo $result;

