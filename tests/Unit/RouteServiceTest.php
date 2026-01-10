<?php

namespace Unit;

use App\Controllers\EventController;
use App\Services\RouteService;
use Exception;
use PHPUnit\Framework\TestCase;

class RouteServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testPost() {
        $service = new RouteService();

        $service->post("test", [
            'controller' => EventController::class,
            'method' => 'create'
        ]);

        $route = $service->find("test", 'POST');

        $this->assertEquals(EventController::class, $route['controller']);
        $this->assertEquals('create', $route['method']);
    }

    /**
     * @throws Exception
     */
    public function testGet() {
        $service = new RouteService();

        $service->get("test", [
            'controller' => EventController::class,
            'method' => 'create'
        ]);

        $route = $service->find("test", 'POST');

        $this->assertEquals(EventController::class, $route['controller']);
        $this->assertEquals('create', $route['method']);
    }

    /**
     * @throws Exception
     */
    public function testPut() {
        $service = new RouteService();

        $service->put("test", [
            'controller' => EventController::class,
            'method' => 'create'
        ]);

        $route = $service->find("test", 'POST');

        $this->assertEquals(EventController::class, $route['controller']);
        $this->assertEquals('create', $route['method']);
    }

    /**
     * @throws Exception
     */
    public function testDelete() {
        $service = new RouteService();

        $service->delete("test", [
            'controller' => EventController::class,
            'method' => 'create'
        ]);

        $route = $service->find("test", 'POST');

        $this->assertEquals(EventController::class, $route['controller']);
        $this->assertEquals('create', $route['method']);
    }

    /**
     * @throws Exception
     */
    public function testFind() {
        $service = new RouteService();

        $service->post("test", [
            'controller' => EventController::class,
            'method' => 'create'
        ]);

        $route = $service->find("test", 'POST');

        $this->assertEquals(EventController::class, $route['controller']);
        $this->assertEquals('create', $route['method']);
    }

    /**
     * @throws Exception
     */
    public function testFindPathFail() {
        $this->expectException(Exception::class);
        $service = new RouteService();

        $service->post("test", [
            'controller' => EventController::class,
            'method' => 'create'
        ]);

        $service->find("undefined", 'POST');
    }
}