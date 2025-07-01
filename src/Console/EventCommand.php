<?php

namespace App\Console;

use App\Storage\EventStorage;
use Dotenv\Dotenv;

class EventCommand
{
	private $storage;

	public function __construct()
	{
		$dotenv = Dotenv::createImmutable(dirname(dirname(__DIR__)));
		$dotenv->load();

		$this->storage =  new EventStorage();
	}

	public function run($args)
	{
		$command =  $args[0] ?? null;

		switch ($command) {
			case 'add':
                $jsonFile = $args[1] ?? '';
                if (!file_exists($jsonFile)) {
                    echo "File not found: $jsonFile\n";
                    break;
                }
                $json = file_get_contents($jsonFile);
                $events = json_decode($json, true);
                if (!is_array($events)) {
                    echo "Error: invalid JSON\n";
                    break;
                }
                // Если передали один объект (не массив), обернем в массив
                if (isset($events['priority'])) {
                    $events = [$events];
                }

                foreach ($events as $event) {
                    if (!is_array($event)) {
                        echo "Warning: skipping invalid event\n";
                        continue;
                    }
                    $this->storage->addEvent($event);
                }
                echo "Events added successfully.\n";
                break;
			case 'clear':
				$this->storage->clearEvents();
				echo "All events cleared.\n";
				break;
			case 'find':
                $jsonFile = $args[1] ?? '';
                if (!file_exists($jsonFile)) {
                    echo "File not found: $jsonFile\n";
                    break;
                }
                $json = file_get_contents($jsonFile);

				$params = json_decode($json ?? '{}', true);

				$bestEvent = $this->storage->findBestMatch($params);
				echo $bestEvent ? "Best Event: " . json_encode($bestEvent, JSON_PRETTY_PRINT) . "\n" : "No matching event found.\n";
				break;
			default:
				echo "Usage: php script.php [add|clear|find] [json]\n";
				break;
		}
	}
}