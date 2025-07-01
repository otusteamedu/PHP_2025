<?php

namespace App;

use App\Storage\RedisStorage;
use App\Storage\MemcacheStorage;
use App\Storage\StorageInterface;
use Dotenv\Dotenv;

class App
{
	private static StorageInterface $storage;

    public static function run() {

    	if (php_sapi_name() === 'cli') {
    		self::handleCli();
		} else {
    		self::handleHttp();
		}
    }

    public static function handleHttp()
	{
		$request = new Request();
		$response = Router::dispatch($request);

		http_response_code($response->getStatusCode());
		foreach ($response->getHeaders() as $key => $value) {
			header("$key: $value");
		}

		echo $response->getBody();
	}

	public static function handleCli()
	{
		global $argv;
		array_shift($argv); // Убираем "bin/console"

		$command = $argv[0] ?? '';
		$args = array_slice($argv, 1);

		$commands = [
			'search_books' => \App\Console\SearchBooksCommand::class, // Добавлена команда
			'event' => \App\Console\EventCommand::class
		];

		if (!isset($commands[$command])) {
			self::printHelp($commands);
			exit(1);
		}

		(new $commands[$command]())->run($args);
	}

	private static function printHelp($commands)
	{
		echo "❌ Неизвестная команда.\n";
		echo "Доступные команды:\n";
		foreach (array_keys($commands) as $cmd) {
			echo "  ➤ $cmd\n";
		}
	}
}