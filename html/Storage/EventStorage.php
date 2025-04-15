<?php


interface EventStorage
{
  public function addEvent(Event $event): void;
  public function clearEvents(): void;
  public function findBestMatch(array $params): ?array;
}