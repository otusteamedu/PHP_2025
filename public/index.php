<?php
/*use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Controllers\HomeController;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->post('/api/add', [HomeController::class, 'add']);
$app->post('/api/answer', [HomeController::class, 'answer']);
$app->post('/api/clear', [HomeController::class, 'clear']);

$app->get('/api/event-conditions', [HomeController::class, 'showEventConditions']);
$app->get('/api/event-priorities', [HomeController::class, 'showEventPriorities']);

$app->run();*/

/*$mongoClient = new MongoDB\Client('mongodb://root:example@mongo:27017');
echo 'here';*/

try {
    // 1. Подключение к MongoDB
    $manager = new MongoDB\Driver\Manager('mongodb://root:example@mongo:27017');
    
    $query = new MongoDB\Driver\Query([]); // Пустой фильтр = все документы
    $cursor = $manager->executeQuery('test_database.users', $query);

    echo '<pre>';
    foreach ($cursor as $doc) {
        print_r($doc);
    }
    echo '</pre>';
    
} catch (Exception $e) {
    echo "Ошибка MongoDB: " . $e->getMessage();
}