<?php declare(strict_types=1);

use App\Tasks\Workers\TaskWorker;

require __DIR__ . '/../src/init.php';

// Запуск воркера
try {
	$worker = new TaskWorker();
	$worker->start();
} catch (Exception $e) {
	echo "Failed to start worker: " . $e->getMessage() . PHP_EOL;
	exit(1);
}