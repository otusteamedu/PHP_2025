<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Infrastructure\Console;

use Dinargab\Homework20\Application\Job\ProcessJob\ProcessJobUseCase;

class ConsoleController extends AbstractConsoleController
{

    public function __construct(
        private ProcessJobUseCase $processJobUseCase,
    )
    {
        parent::__construct();
    }

    protected function handle(): int
    {
        $this->line("Started Processing");
        ($this->processJobUseCase)();
        return 0;
    }
}