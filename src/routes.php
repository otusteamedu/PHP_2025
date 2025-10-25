<?php
declare(strict_types=1);

use Slim\App;
use App\Controller\ReportController;
use App\Service\ReportService;
use App\Repository\ReportRepository;

return function (App $app): void {
    $pdo = new PDO('sqlite:' . $_ENV['SQLITE_PATH']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $redis = new Redis();
    $redis->connect($_ENV['REDIS_HOST'], (int)$_ENV['REDIS_PORT']);

    $repository = new ReportRepository($pdo);
    $service = new ReportService($repository, $redis);
    $controller = new ReportController($service);

    $app->post('/report', [$controller, 'create']);
    $app->get('/report/{id}', [$controller, 'status']);
};
