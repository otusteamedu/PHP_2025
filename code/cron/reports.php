<?php

declare(strict_types=1);
error_reporting(E_ALL & ~E_DEPRECATED);

require_once __DIR__ . '../../vendor/autoload.php';
use Ak\Hw\Controllers\UserReportController;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__. '../../');
$dotenv->load();

$userReportObj = new UserReportController();
$userReportObj->queueHandler();