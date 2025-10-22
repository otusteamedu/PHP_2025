<?php

namespace Blarkinov\PhpDbCourse\Http;

use Blarkinov\PhpDbCourse\Bin\Dispatcher;
use Blarkinov\PhpDbCourse\Bin\Router;
use Blarkinov\PhpDbCourse\Controllers\MainController;
use Blarkinov\PhpDbCourse\Service\Validator;
use Throwable;

class Request
{

    private Response $response;
    private MainController $mainController;

    public function __construct()
    {
        $this->response = new Response();
        $this->mainController = new MainController();
    }

    public function handle()
    {

        $routes = include __DIR__ . '/../Config/route.php';

        try {

            if (!(new Validator)->mainValidate()) {
                $this->mainController->badRequest();
                return;
            }

            $track = (new Router())->getTrack($routes, $_SERVER['REQUEST_URI'],$_SERVER['REQUEST_METHOD']);
            $result = (new Dispatcher)->getResult($track);

            $this->response->send(200, $result);
        } catch (\Throwable $th) {
            $this->mainController->badRequest($th->getMessage());
        }
    }
}
