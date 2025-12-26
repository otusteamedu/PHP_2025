<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Infrastructure\Console;

abstract class AbstractConsoleController
{
    protected array $args;

    public function __construct()
    {
        global $argv;
        $this->args = array_slice($argv, 1);
    }

    public function run(): void
    {
        try {
            // Execute the child logic
            $exitCode = $this->handle();
            exit($exitCode);
        } catch (\Exception $e) {
            $this->line("Exception: " . $e->getMessage());
            exit(1);
        }
    }


    abstract protected function handle(): int;

    protected function getArg(int $index, $default = null): ?string
    {
        return $this->args[$index] ?? $default;
    }


    protected function line(string $message): void
    {
        echo $message . PHP_EOL;
    }

    protected function success(string $message): void
    {
        echo "\033[32m" . $message . "\033[0m" . PHP_EOL;
    }

}