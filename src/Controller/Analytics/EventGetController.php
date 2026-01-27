<?php

namespace App\Controller\Analytics;

use App\Http\Request;
use App\Http\Response;
use App\Provider\Event\IEventRepository;

class EventGetController
{
    private IEventRepository $repository;

    public function __construct(IEventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request): \App\Http\Response
    {
        $body = $request->getArrayBody();
        if (isset($body['conditions']))
        {
            $conditions = $body['conditions'];
        }

        if (!isset($conditions))
        {
            throw new \App\Exception\HttpException('Параметр "conditions" является обязательным');
        }

        $result = $this->repository->findEventByConditions($conditions);

        return (new Response(
            [
                'data' => [
                    'event' => $result,
                ]
            ],
            $result ? 200: 404)
        );
    }
}