<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Container\ContainerBuilder;
use App\Presentation\Controllers\EmailVerificationController;

$container = ContainerBuilder::build();
$controller = $container->get(EmailVerificationController::class);
$controller->run();
