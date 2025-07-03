<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdo = new PDO('sqlite:' . $_ENV['SQLITE_PATH']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$redis = new Redis();
$redis->connect($_ENV['REDIS_HOST'], (int)$_ENV['REDIS_PORT']);

$repository = new App\Repository\ReportRepository($pdo);
$service = new App\Service\ReportService($repository, $redis);

echo "Worker started...\n";

while (true) {
    $id = $redis->lPop('report_queue');
    if ($id !== false) {
        echo "Processing report id: $id\n";
        $service->processReport((int)$id);
    } else {
        sleep(1);
    }
}
