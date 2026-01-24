<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Presentation\Controllers\App;

$response = App::build()->run();
echo $response->send();