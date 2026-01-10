<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Aovchinnikova\Hw15\Controller\EmailController;

// Нарушение: Жесткое создание контроллера (нет DI-контейнера).
// Решение: Использовать контейнер зависимостей.
$controller = new EmailController();
$controller->handleValidationRequest();

// Правильный код:
// $container = new DIContainer();
// $controller = $container->get(EmailController::class);
// $controller->handleValidationRequest();
