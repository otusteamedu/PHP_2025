<?php

declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "This script can only be run from CLI." . PHP_EOL);
    exit(1);
}

$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    fwrite(STDERR, "Dependencies are not installed. Run 'composer install'." . PHP_EOL);
    exit(1);
}

require_once $autoloadPath;

if (file_exists(__DIR__ . '/../.env')) {
    $dotenvClass = 'Dotenv\\Dotenv';
    if (class_exists($dotenvClass)) {
        $dotenv = new $dotenvClass(__DIR__ . '/..');
        $dotenv->load();
    }
}

use App\Config\AppConfig;
use App\Config\DatabaseConfig;
use App\Hikvision\HikvisionClient;
use App\Repository\UserRepository;
use App\Repository\VisitRepository;
use App\Service\AttendanceAnalyzer;
use App\Service\SKYDService;

$command = $argv[1] ?? 'help';

try {
    $service = buildService();

    switch ($command) {
        case 'sync:users':
            $users = $service->syncUsers();
            fwrite(STDOUT, 'Users synchronized: ' . count($users) . PHP_EOL);
            exit(0);

        case 'help':
            printHelp();
            exit(0);

        default:
            fwrite(STDERR, 'Unknown command: ' . $command . PHP_EOL);
            printHelp();
            exit(1);
    }
} catch (Throwable $exception) {
    fwrite(STDERR, 'Command failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}

function buildService(): SKYDService
{
    $dbConfig = new DatabaseConfig();
    $appConfig = new AppConfig();

    $client = new HikvisionClient();
    $userRepo = new UserRepository($dbConfig);
    $visitRepo = new VisitRepository($dbConfig);
    $analyzer = new AttendanceAnalyzer($appConfig);

    return new SKYDService($client, $userRepo, $visitRepo, $analyzer, $appConfig);
}

function printHelp(): void
{
    fwrite(STDOUT, "SKYD CLI commands:" . PHP_EOL);
    fwrite(STDOUT, "  php bin/cli.php sync:users" . PHP_EOL);
    fwrite(STDOUT, "  php bin/cli.php help" . PHP_EOL);
}
