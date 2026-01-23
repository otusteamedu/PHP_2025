<?php

namespace Blarkinov\RedisCourse\Controllers;

use Blarkinov\RedisCourse\Http\Response;
use Blarkinov\RedisCourse\Models\Event\WorkerEvent;
use Blarkinov\RedisCourse\Service\Redis;
use Blarkinov\RedisCourse\Service\RedisSearch;
use Blarkinov\RedisCourse\Service\Validator;

class EventController
{
    private Response $response;
    private Redis $redis;
    private Validator $validator;

    public function __construct()
    {
        $this->redis = new Redis();
        $this->response = new Response();
        $this->validator = new Validator();
    }

    public function save()
    {
        try {
            $this->validator->eventSave();

            $event = WorkerEvent::createEvent($_POST['priority'], $_POST['conditions'], $_POST['data']);

            WorkerEvent::save($event);

            $this->response->send(200, ['status' => 'success', 'operation' => 'save event']);
        } catch (\Throwable $th) {
            $this->response->send(400, ['status' => 'failed', 'operation' => 'save event', 'error' => $th->getMessage()]);
        }
    }

    public function priority()
    {
        try {
            $this->validator->eventPriority();

            $event = WorkerEvent::getPriorityEvents($_POST['conditions']);

            if ($event)
                $event = [
                    'priority' => $event->getPriority(),
                    'conditions' => $event->getConditions(),
                    'data' => $event->getData(),
                    'uuid' => $event->getUuid(),
                ];


            $this->response->send(
                200,
                [
                    'status' => 'success',
                    'operation' => 'get priority event',
                    'event' => $event,
                ]
            );
        } catch (\Throwable $th) {
            $this->response->send(400, ['status' => 'failed', 'operation' => 'get priority event', 'error' => $th->getMessage()]);
        }
    }

    public function destroy()
    {
        try {
            $this->redis->destroy();
            $this->response->send(200, ['status' => 'success', 'operation' => 'clear']);
        } catch (\Throwable $th) {
            $this->response->send(400, ['status' => 'failed', 'operation' => 'clear', 'error' => $th->getMessage()]);
        }
    }
}
