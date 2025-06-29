<?php
use App\Services\ServiceInterface;
use Slim\Factory\AppFactory;
use App\Controllers\HomeController;
use DI\Container;
use Psr\Container\ContainerInterface;
use App\Services\RedisService;
use App\Services\MongoService;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
$container->set(ServiceInterface ::class, function (ContainerInterface $container) {
    $driver = getenv('DRIVER');
    return $driver == 'redis' ? new RedisService() : new MongoService();
});
$app = AppFactory::createFromContainer($container);

$app->post('/api/add', [HomeController::class, 'add']);
$app->post('/api/answer', [HomeController::class, 'answer']);
$app->post('/api/clear', [HomeController::class, 'clear']);

$app->get('/api/events', [HomeController::class, 'events']);

$app->run();