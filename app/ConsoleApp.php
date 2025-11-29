<?php declare(strict_types=1);

namespace EManager;

use EManager\Storage\StorageFactory;

class ConsoleApp
{
	private EventManager $eventManager;
	private CommandHandler $commandHandler;

	public function __construct(string $storageType)
	{
		$storage = StorageFactory::create($storageType);
		$this->eventManager = new EventManager($storage);
		$this->commandHandler = new CommandHandler($this->eventManager);
	}

	public function run(array $argv): void
	{
		array_shift($argv); // Remove script name

		try {
			$this->commandHandler->handle($argv);
		} catch (\Exception $e) {
			echo "Error: " . $e->getMessage() . "\n";
		}
	}
}