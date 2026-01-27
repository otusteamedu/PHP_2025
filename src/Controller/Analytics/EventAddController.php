<?php

namespace App\Controller\Analytics;

use App\Entity\Analytics\Event;
use App\Http\Request;
use App\Http\Response;
use App\Provider\Event\IEventRepository;

class EventAddController
{

    private IEventRepository $repository;

    public function __construct(IEventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request): \App\Http\Response
    {
        $body = $request->getArrayBody();
        $priority = null;
        if (isset($body['priority']))
        {
            $priority = $body['priority'];

        }

        if (isset($body['event']))
        {
            $event = $body['event'];

        }

        if (isset($body['conditions']))
        {
            $conditions = $body['conditions'];

        }

        if (!isset($conditions))
        {
            throw new \App\Exception\HttpException('Параметр "conditions" является обязательным');
        }

        if (!isset($event))
        {
            throw new \App\Exception\HttpException('Параметр "event" является обязательным');
        }

        $result = $this->repository->add(new Event($event, $conditions, $priority));

        return (new Response(
            ['message' => $result ? 'Событие добавлено в систему' : 'Произошла ошибка при добавлении'],
            $result ? 200: 400)
        );
    }
}