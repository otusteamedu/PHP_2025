<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Blarkinov\ElasticApp\App\App;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__."/../");
$dotenv->load();

(new App)->run($argv);
