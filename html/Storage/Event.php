<?php

class Event
{
  public int $priority;
  public array $conditions;
  public array $event;

  public function __construct(int $priority, array $conditions, array $event)
  {
    $this->priority = $priority;
    $this->conditions = $conditions;
    $this->event = $event;
  }

  public function matches(array $params): bool
  {
    foreach ($this->conditions as $key => $value) {
      if (!isset($params[$key]) || $params[$key] != $value) {
        return false;
      }
    }
    return true;
  }
}