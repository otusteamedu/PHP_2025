<?php

declare(strict_types=1);

namespace App\Application\Console;

use App\Application\Console\Controller\AddEventController;
use App\Application\Console\Controller\ClearEventsController;
use App\Application\Console\Controller\FindEventController;
use App\Domain\Service\EventService;
use App\Infrastructure\Storage\EventStorageFactory;
use InvalidArgumentException;
use Throwable;

/**
 * Точка входа консольного приложения и маршрутизатор команд
 */
final class App
{
    /**
     * Запускает консольную команду по переданным argv
     *
     * @param array<int, string> $argv
     */
    public function run(array $argv): void
    {
        try {
            $command = $argv[1] ?? null;

            if ($command === null || in_array($command, ['help', '--help', '-h'], true)) {
                $this->printHelp();
                return;
            }

            $service = $this->createEventService();
            $arguments = new InputArguments($this->parseOptions(array_slice($argv, 2)));

            $result = match ($command) {
                'add' => (new AddEventController($service))->handle($arguments),
                'clear' => (new ClearEventsController($service))->handle($arguments),
                'find' => (new FindEventController($service))->handle($arguments),
                default => throw new InvalidArgumentException('Unknown command "' . $command . '".'),
            };

            $this->printJson($result);
        } catch (Throwable $exception) {
            echo 'Error: ' . $exception->getMessage() . PHP_EOL;
            exit(1);
        }
    }

    private function createEventService(): EventService
    {
        return new EventService(
            (new EventStorageFactory())->create(),
        );
    }

    /**
     * Разбирает аргументы формата --name=value в ассоциативный массив
     *
     * @param array<int, string> $args
     *
     * @return array<string, string>
     */
    private function parseOptions(array $args): array
    {
        $options = [];

        foreach ($args as $arg) {
            if (!str_starts_with($arg, '--') || !str_contains($arg, '=')) {
                throw new InvalidArgumentException('Invalid option "' . $arg . '". Use --name=value format.');
            }

            [$name, $value] = explode('=', substr($arg, 2), 2);

            if ($name === '') {
                throw new InvalidArgumentException('Option name cannot be empty.');
            }

            $options[$name] = $value;
        }

        return $options;
    }

    /**
     * Выводит конечный результат команды в JSON
     *
     * @param array<string, mixed> $data
     */
    private function printJson(array $data): void
    {
        echo json_encode($data) . PHP_EOL;
    }

    private function printHelp(): void
    {
        echo <<<'HELP'
Usage:
  php index.php add --priority=1000 --conditions='{\"param1\":1}' --event='{\"name\":\"event_1\"}'
  php index.php clear
  php index.php find --params='{\"param1\":1,\"param2\":2}'

Commands:
  add    Add a new event to storage.
  clear  Remove all events from storage.
  find   Find the highest priority event matching params.

HELP;
    }
}
