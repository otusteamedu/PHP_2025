<?php

declare(strict_types=1);

namespace App\UserInterface\Message;

use App\Application\ProcessTaskMessage\ProcessTaskMessageHandler;
use App\Application\ProcessTaskMessage\ProcessTaskMessageQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TaskMessageHandler
{

    public function __construct(
        private ProcessTaskMessageHandler $handler,
    )
    {

    }

    public function __invoke(TaskMessage $message): void
    {
        $query = new ProcessTaskMessageQuery($message->id);

        $this->handler->__invoke($query);
    }

}
