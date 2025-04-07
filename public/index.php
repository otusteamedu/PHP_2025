<?php declare(strict_types=1);

use App\App;
use App\Storage\RedisStore;

require(__DIR__ . '/../vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$redisStore = new RedisStore();
$app = new App($redisStore);
$response = $app->run();

echo $response;
