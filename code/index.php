<?php

declare(strict_types=1);

use App\Controller\StatementRequestController;
use App\Presenter\StatementRequestPresenter;
use App\Queue\QueuePublisherFactory;

require __DIR__ . '/vendor/autoload.php';

try {
    $publisherFactory = new QueuePublisherFactory();
    $presenter = new StatementRequestPresenter(__DIR__ . '/templates');
    $controller = new StatementRequestController($publisherFactory, $presenter);

    echo $controller->handle($_SERVER['REQUEST_METHOD'] ?? 'GET', $_POST);
} catch (Throwable $exception) {
    error_log((string) $exception);
    http_response_code(500);
    echo 'Произошла ошибка при обработке запроса. Попробуйте позже.';
}
