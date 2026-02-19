<?php

declare(strict_types=1);

use Pryaniki\App\Presentation\Console\Input\ArgEmailInput;
use Pryaniki\App\Presentation\Console\ConsoleEmailValidatorRunner;

require '../app/vendor/autoload.php';

$email = ArgEmailInput::getEmail();
$app = new ConsoleEmailValidatorRunner();

$result = $app->run($email);
echo $result;

