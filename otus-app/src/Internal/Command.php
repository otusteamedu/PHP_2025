<?php

namespace App\Internal;

use App\Command\ConsumeEvent;
use App\Enum\QueueNameEnum;
use App\Service\EventService;
use App\Service\RabbitService;
use App\Service\RedisService;
use Exception;
use Throwable;

class Command
{
    public function run(array $argv): string
    {
        try {
            $action = $argv[1] ?? null;
            $subAction = $argv[2] ?? null;

            switch ($action) {
                case 'consumer:run':
                    switch ($subAction) {
                        case QueueNameEnum::EVENT_QUEUE->value:
                            $queueService = new RabbitService();

                            $queueService->consume(QueueNameEnum::EVENT_QUEUE, function ($data) {
                                $eventService = new EventService(new RabbitService(), new RedisService());
                                $eventConsumer = new ConsumeEvent($eventService);

                                $eventConsumer->process($data);
                            });

                            break;
                        default:
                            throw new Exception("Unknown subaction: $subAction");
                    }

                default:
                    throw new Exception("Unknown action: $action");
            }
        } catch (Throwable $e) {
            return 'Invalid request ' . $e->getMessage();
        }
    }
}
