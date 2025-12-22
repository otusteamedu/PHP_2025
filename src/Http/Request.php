<?php

namespace Blarkinov\RabbitMq\Http;

use Blarkinov\RabbitMq\Exceptions\BadRequestException;
use Blarkinov\RabbitMq\Router;
use Blarkinov\RabbitMq\Service\Validator;
use Exception;


class Request
{

    private Response $response;
    private Validator $validator;

    public function __construct()
    {
        $this->response = new Response;
        $this->validator = new Validator;
    }

    public function handle(array $routes)
    {
        try {
            $router = new Router();

            foreach ($routes as $route) {
                $router->add($route['method'], $route['pattern'], $route['handler']);
            }

            $this->validator->mainValidate();

            $router->dispatch(
                $_SERVER['REQUEST_METHOD'],
                parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
            );
        } catch (BadRequestException $e) {
            $this->response->send(400, 'Bad Request');
        } catch (Exception $e) {
            $this->response->send(400, $e->getMessage());
        }
    }
}
