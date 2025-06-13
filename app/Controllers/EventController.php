<?php

namespace App\Controllers;

use App\DTO\EventDTO;
use App\Http\Response;
use App\Repositories\RedisRepository;
use App\Validators\EventControllerValidator;
use RedisException;

class EventController extends Controller
{
    /** @var string|null */
    protected ?string $validator = EventControllerValidator::class;

    /** @var RedisRepository */
    protected RedisRepository $redisRepository;

    public function __construct() {
        $this->redisRepository = (new RedisRepository());
    }

    /**
     * @throws RedisException
     */
    public function get(): Response {
        $params = $this->request->getData()['params'];
        $event = $this->redisRepository->getEvent(new EventDTO(
            null,
            [],
            $params
        ));

        if (empty($event)) {
            $response = new Response([], 200, "События удовлетворяющие условиям не были найдены");
        } else {
            $response = new Response($event, 200, "Событие найдено");
        }

        return $response;
    }

    /**
     * @return Response
     * @throws RedisException
     */
    public function create(): Response {
        $data = $this->request->getData();

        $this->redisRepository->createEvent(new EventDTO(
            $data['priority'],
            $data['event'],
            $data['conditions'],
        ));

        return new Response([], 200, "Событие создано");
    }

    /**
     * @return Response
     * @throws RedisException
     */
    public function delete(): Response {
        $this->redisRepository->truncateEvent();
        return new Response([], 200, "События были удалены");
    }
}