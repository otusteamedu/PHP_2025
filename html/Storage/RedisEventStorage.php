<?php

class RedisEventStorage implements EventStorage
{
  private Redis $redis;
  private string $keyPrefix;

  public function __construct(Redis $redis, string $keyPrefix = 'events:')
  {
    $this->redis = $redis;
    $this->keyPrefix = $keyPrefix;
  }

  public function addEvent(Event $event): void
  {
    $id = uniqid();
    $key = $this->keyPrefix . $id;

    $data = [
        'priority' => $event->priority,
        'conditions' => json_encode($event->conditions),
        'event' => json_encode($event->event)
    ];

    $this->redis->hMSet($key, $data);
  }

  public function clearEvents(): void
  {
    $keys = $this->redis->keys($this->keyPrefix . '*');
    if (!empty($keys)) {
      $this->redis->del($keys);
    }
  }

  public function findBestMatch(array $params): ?array
  {
    $keys = $this->redis->keys($this->keyPrefix . '*');
    $bestMatch = null;
    $maxPriority = -1;

    foreach ($keys as $key) {
      $data = $this->redis->hGetAll($key);

      $conditions = json_decode($data['conditions'], true);
      $event = json_decode($data['event'], true);
      $priority = (int)$data['priority'];

      $matches = true;
      foreach ($conditions as $condKey => $condValue) {
        if (!isset($params[$condKey]) || $params[$condKey] != $condValue) {
          $matches = false;
          break;
        }
      }

      if ($matches && $priority > $maxPriority) {
        $maxPriority = $priority;
        $bestMatch = $event;
      }
    }

    return $bestMatch;
  }
}