<?php

declare(strict_types=1);

namespace App;

use App\Controllers\EventsController;
use App\Http\Request;
use App\Http\Response;
use App\Storage\EventsStorageInterface;
use App\Storage\MemcachedEventsStorage;
use App\Storage\RedisEventsStorage;

class App
{
    private Request $request;
    private Response $response;
    private EventsStorageInterface $storage;
    private EventsController $controller;

    public function __construct(?EventsStorageInterface $storage = null)
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->storage = $storage ?? $this->resolveStorage();
        $this->controller = new EventsController($this->request, $this->response, $this->storage);
    }

    private function resolveStorage(): EventsStorageInterface
    {
        $driver = getenv('EVENTS_STORAGE');

        if ($driver === 'memcached') {
            return new MemcachedEventsStorage();
        }

        return new RedisEventsStorage();
    }

    public function run(): string
    {
        $path = $this->request->getPath();

        if ($path === '/events' && $this->request->isPost()) {
            return $this->controller->addEvent();
        }

        if ($path === '/events' && $this->request->isDelete()) {
            return $this->controller->clearEvents();
        }

        if ($path === '/events/match' && $this->request->isPost()) {
            return $this->controller->matchEvent();
        }

        return $this->response->error(404, 'Маршрут не найден. Используйте POST /events, DELETE /events или POST /events/match.');
    }
}
