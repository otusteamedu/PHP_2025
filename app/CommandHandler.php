<?php declare(strict_types=1);

namespace EManager;

class CommandHandler
{
	private EventManager $eventManager;

	public function __construct(EventManager $eventManager)
	{
		$this->eventManager = $eventManager;
	}

	public function handle(array $args): void
	{
		$command = $args[0] ?? null;

		switch ($command) {
			case 'add':
				$this->handleAddCommand($args);
				break;
			case 'clear':
				$this->eventManager->clearEvents();
				echo "Все события были очищены.\n";
				break;
			case 'find':
				$this->handleFindCommand($args);
				break;
			default:
				$this->showHelp();
		}
	}

	private function handleAddCommand(array $args): void
	{
		if (!isset($args[1])) {
			throw new \InvalidArgumentException('Для добавления команды требуется событие в формате JSON');
		}

		$eventData = json_decode($args[1], true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \InvalidArgumentException('Недопустимый формат JSON');
		}

		$this->eventManager->addEvent($eventData);
		echo "Событие было успешно добавлено.\n";
	}

	private function handleFindCommand(array $args): void
	{
		if (!isset($args[1])) {
			throw new \InvalidArgumentException('Params JSON is required for find command');
		}

		$matching = json_decode($args[1], true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \InvalidArgumentException('Недопустимый формат JSON');
		}

		$event = $this->eventManager->findBestMatchingEvent($matching);

		if ($event) {
			echo "Найдено подходящее событие:\n";
			echo json_encode($event, JSON_PRETTY_PRINT) . "\n";
		} else {
			echo "Совпадающих событий не найдено.\n";
		}
	}

	private function showHelp(): void
	{
		echo "Event Manager Console Application\n";
		echo "Usage:\n";
		echo "  php bin/console <storage> add '<event>'\n";
		echo "  php bin/console <storage> clear\n";
		echo "  php bin/console <storage> find '<message>'\n";
	}
}