<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\OrderController;
use App\Views\View;

require __DIR__ . '/../vendor/autoload.php';

// Load environment
$envPath = dirname(__DIR__);
if (file_exists($envPath . '/.env')) {
    Dotenv\Dotenv::createImmutable($envPath)->load();
}

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(require __DIR__ . '/../src/config/container.php');
$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

// Error middleware (simple)
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Routes
$app->get('/', function (Request $request, Response $response) use ($container) {
    /** @var View $view */
    $view = $container->get(View::class);
    $content = $view->render('home');
    $response->getBody()->write($content);
    return $response;
});

$app->post('/order', [OrderController::class, 'create']);

$app->run();
