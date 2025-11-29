<?php declare(strict_types=1);

namespace EManager;

use EManager\Storage\StorageInterface;

class EventManager
{
	private StorageInterface $storage;

	public function __construct(StorageInterface $storage)
	{
		$this->storage = $storage;
	}

	public function addEvent(array $eventData): void
	{
		$this->validateEvent($eventData);
		$this->storage->addEvent($eventData);
	}

	public function clearEvents(): void
	{
		$this->storage->clearEvents();
	}

	public function findBestMatchingEvent(array $params): ?array
	{
		return $this->storage->findMatchingEvent($params);
	}

	private function validateEvent(array $eventData): void
	{
		if (!isset($eventData['priority'], $eventData['conditions'], $eventData['event'])) {
			throw new \InvalidArgumentException('Недопустимая структура события');
		}

		if (!is_int($eventData['priority'])) {
			throw new \InvalidArgumentException('Приоритет должен быть целым числом');
		}

		if (!is_array($eventData['conditions']) && !is_array($eventData['event'])) {
			throw new \InvalidArgumentException('Условия и событие должны быть массивами');
		}
	}
}