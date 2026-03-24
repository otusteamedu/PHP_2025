<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
    $dotenv->load();
}

use App\Config\AppConfig;
use App\Config\DatabaseConfig;
use App\Controller\SKYDController;
use App\Hikvision\HikvisionClient;
use App\Repository\UserRepository;
use App\Repository\VisitRepository;
use App\Report\ExcelReportGenerator;
use App\Service\AttendanceAnalyzer;
use App\Service\SKYDService;

// Конфиги
$dbConfig = new DatabaseConfig();
$appConfig = new AppConfig();

// Сервисы
$client = new HikvisionClient();
$userRepo = new UserRepository($dbConfig);
$visitRepo = new VisitRepository($dbConfig);
$analyzer = new AttendanceAnalyzer($appConfig);
$service = new SKYDService($client, $userRepo, $visitRepo, $analyzer, $appConfig);
$excelGenerator = new ExcelReportGenerator();

$controller = new SKYDController($service, $excelGenerator);
$controller->handleRequest();