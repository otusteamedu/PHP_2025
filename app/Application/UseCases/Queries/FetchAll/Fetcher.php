<?php

namespace App\Application\UseCases\Queries\FetchAll;

use App\Application\EventRepositoryInterface;

class Fetcher
{
    public function __construct(
        private EventRepositoryInterface $repository,
    ) {}

    public function fetch(): string
    {
        $events = $this->repository->fetchAll();

        $message = '';
        foreach ($events as $event) {
            $arr = [
                'event' => $event->getEventName()->toString(), 
                'conditions' => $event->getConditions()->toArray(), 
                'priority' => $event->getPriority()->toInt()
            ];
            $message .= json_encode($arr) . PHP_EOL;
        }
        $message = !empty($message) ? $message : 'Нет зарегистрированных событий';

        return $message;
    }
}