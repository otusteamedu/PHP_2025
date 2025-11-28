<?php
declare(strict_types=1);

namespace Dinargab\Homework19\Infrastructure\Console;

use Dinargab\Homework19\Application\Receiver\UseCase\ProcessQueueUseCase;

class RecieverController extends AbstractConsoleController
{

    public function __construct(
        private readonly ProcessQueueUseCase $processQueueUseCase,
    )
    {
        parent::__construct();
    }

    protected function handle(): int
    {
        ($this->processQueueUseCase)();
        return 0;
    }
}