<?php declare(strict_types=1);

use App\App;
use App\Exception\RedisConnectionException;
use App\Http\Response;
use App\Storage\RedisStore;

require(__DIR__ . '/../vendor/autoload.php');

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    $redisStore = new RedisStore();
    $app = new App($redisStore);
    $response = $app->run();
} catch (RedisConnectionException $e) {
    $response = (new Response())->send(503, ['error' => 'Service unavailable']);
} catch (\Throwable $e) {
    $response = (new Response())->send(500, ['error' => 'Internal server error']);
}

echo $response;
