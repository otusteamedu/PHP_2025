<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Command\CommandInterface;
use App\Console\IO\ConsoleOutput;

final class ConsoleApplication
{
    private array $commands = [];
    private static array $paramsApp = [];

    public function __construct(public readonly array $params)
    {
        self::$paramsApp = $params;
    }

    public static function getParams(): array
    {
        return self::$paramsApp;
    }

    public function add(CommandInterface $command): void
    {
        $this->commands[$command->getName()] = $command;
    }

    public function run(array $argv): string
    {
        $output = new ConsoleOutput();

        $commandName = $argv[1] ?? null;
        if ($commandName === null || $commandName === 'help') {
            return $this->renderHelp($output);
        }

        if (!isset($this->commands[$commandName])) {
            $output->error(sprintf('Unknown command "%s".', $commandName));
            return $this->renderHelp($output);
        }

        $input = $this->parseOptions(array_slice($argv, 2));

        return $this->commands[$commandName]->execute($input, $output);
    }

    private function renderHelp(ConsoleOutput $output): string
    {
        $message = $output->writeln(self::$paramsApp['app_name'] . ' v' . self::$paramsApp['app_version']);
        $message .= $output->writeln('Available commands:');

        foreach ($this->commands as $command) {
            $message .= $output->writeln(sprintf('  %s - %s', $command->getName(), $command->getDescription()));
        }

        return $message;
    }

    /**
     * @param string[] $args
     * @return array<string, string|bool>
     */
    private function parseOptions(array $args): array
    {
        $options = [];

        for ($i = 0, $count = count($args); $i < $count; $i++) {
            $arg = $args[$i];

            if (str_starts_with($arg, '--')) {
                $trimmed = substr($arg, 2);

                if (strpos($trimmed, '=') !== false) {
                    [$key, $value] = explode('=', $trimmed, 2);
                    $options[$key] = $value;
                    continue;
                }

                $next = $args[$i + 1] ?? null;
                if ($next !== null && !str_starts_with($next, '--')) {
                    $options[$trimmed] = $next;
                    $i++;
                    continue;
                }

                $options[$trimmed] = true;
            }
        }

        return $options;
    }
}
