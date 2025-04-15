<?php

namespace App\Controller;

require_once __DIR__ . '/../Storage/Event.php';
require_once __DIR__ . '/../Storage/EventStorage.php';
require_once __DIR__ . '/../Storage/RedisEventStorage.php';

use App\Storage\Event;
use App\Storage\RedisEventStorage;
use App\Exceptions\ApiException;
use Redis;

class EventController
{
  private RedisEventStorage $storage;

  public function __construct()
  {
    $redis = new Redis();
    if (!$redis->connect('redis', 6379)) {
      throw new ApiException('Redis connection failed', 500);
    }

    $this->storage = new RedisEventStorage($redis);
  }

  public function handleRequest(): void
  {
    header('Content-Type: application/json');

    try {
      $input = json_decode(file_get_contents('php://input'), true) ?? [];
      $action = $_GET['action'] ?? '';

      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new ApiException('Only POST requests are allowed', 405);
      }

      switch ($action) {
        case 'add':
          $this->handleAdd($input);
          break;

        case 'match':
          $this->handleMatch($input);
          break;

        case 'clear':
          $this->handleClear();
          break;

        default:
          throw new ApiException('Invalid action specified', 404);
      }
    } catch (ApiException $e) {
      http_response_code($e->getCode());
      echo json_encode(['error' => $e->getMessage()]);
    } catch (\Exception $e) {
      http_response_code(500);
      echo json_encode(['error' => 'Internal server error']);
    }
  }

  private function handleAdd(array $input): void
  {
    if (empty($input['priority']) || empty($input['conditions']) || empty($input['event'])) {
      throw new ApiException('Missing required fields', 400);
    }

    $event = new Event(
        (int)$input['priority'],
        $input['conditions'],
        $input['event']
    );

    $this->storage->addEvent($event);
    echo json_encode(['status' => 'success']);
  }

  private function handleMatch(array $input): void
  {
    if (empty($input['params'])) {
      throw new ApiException('Missing params field', 400);
    }

    $bestMatch = $this->storage->findBestMatch($input['params']);
    echo json_encode(['event' => $bestMatch]);
  }

  private function handleClear(): void
  {
    $this->storage->clearEvents();
    echo json_encode(['status' => 'success']);
  }
}