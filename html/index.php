<?php
require_once __DIR__ . '/src/Controller/EventController.php';

use App\Controller\EventController;

header('Content-Type: application/json');

try {
  $controller = new EventController();
  $controller->handleRequest();
} catch (Throwable $e) {
  http_response_code($e->getCode() ?: 500);
  echo json_encode([
      'error' => $e->getMessage(),
      'code' => $e->getCode() ?: 500
  ]);
}