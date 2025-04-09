<?php

namespace App\Controllers;

use App\Http\Response;
use App\Services\RedisService;
use App\Validators\EventControllerValidator;
use RedisException;

class EventController extends Controller
{
    /** @var string|null */
    protected ?string $validator = EventControllerValidator::class;

    /** @var RedisService */
    protected RedisService $redisService;

    public function __construct() {
        $this->redisService = (new RedisService);
    }


    /**
     * @throws RedisException
     */
    public function get(): Response {
        $params = $this->request->getData()['params'];
        $event = $this->redisService->getEvent($params);

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

        $this->redisService->createEvent($data);

        return new Response([], 200, "Событие создано");
    }

    /**
     * @return Response
     * @throws RedisException
     */
    public function delete(): Response {
        $this->redisService->truncateEvent();
        return new Response([], 200, "События были удалены");
    }
}