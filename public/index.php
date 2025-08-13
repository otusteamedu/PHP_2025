<?php
use App\Application\EventRepositoryInterface;
use App\Infrastructure\Repositories\MongoEventRepository;
use App\Infrastructure\Repositories\RedisEventRepository;
use Slim\Factory\AppFactory;
use App\Infrastructure\Controllers\HomeController;
use DI\Container;
use Psr\Container\ContainerInterface;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
$container->set(EventRepositoryInterface::class, function (ContainerInterface $container) {
    $driver = getenv('DRIVER');
    return $driver == 'redis' ? new RedisEventRepository() : new MongoEventRepository();
});

$app = AppFactory::createFromContainer($container);

$app->post('/api/add', [HomeController::class, 'add']);
$app->post('/api/answer', [HomeController::class, 'answer']);
$app->post('/api/clear', [HomeController::class, 'clear']);

$app->get('/api/events', [HomeController::class, 'events']);

$app->run();