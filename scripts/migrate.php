<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Database;
use Dotenv\Dotenv;

$root = dirname(__DIR__);
if (file_exists($root . '/.env')) {
    $dotenv = Dotenv::createImmutable($root);
    $dotenv->load();
    $dotenv->required([
        'DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS',
    ]);;
}

$db = new Database($_ENV['DB_HOST'], (int) $_ENV['DB_PORT'], $_ENV['DB_NAME'],
    $_ENV['DB_USER'], $_ENV['DB_PASS']);
$pdo = $db->pdo();

$dir = $root . '/migrations';
$files = glob($dir . '/*.sql');
sort($files);

foreach ($files as $file) {
    echo 'Applying migration: ' . basename($file) . PHP_EOL;
    $sql = file_get_contents($file);
    $pdo->exec($sql);
}

echo "Migrations applied." . PHP_EOL;
