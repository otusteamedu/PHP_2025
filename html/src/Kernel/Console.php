<?php

declare(strict_types=1);

namespace Otus\Cache\Kernel;

class Console extends AbstractKernel
{
    /**
     * @param array $handlers
     */
    public function __construct(
        protected array $handlers = [],
    ) {
        parent::__construct();
    }

    /**
     * @param string $command
     * @param callable $handler
     */
    public function register(string $command, callable $handler): void
    {
        $this->handlers[$command] = $handler;
    }

    /**
     * @param string $command
     * @param array $args
     *
     * @return int
     */
    public function handle(string $command, array $args = []): int
    {
        return $this->handlers[$command](...$args);
    }

    /**
     * @param array $argv
     *
     * @return int
     */
    public function run(array $argv): int
    {
        $command = $argv[1] ?? null;
        $args = array_slice($argv, 2);

        if (!$command || !array_key_exists($command, $this->handlers)) {
            $this->help();

            return 1;
        }

        return $this->handle($command, $args);
    }

    protected function help(): void
    {
        echo 'Handlers : ', PHP_EOL;

        foreach ($this->handlers as $command => $handler) {
            echo $command, PHP_EOL;
        }
    }
}
