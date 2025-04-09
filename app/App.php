<?php

namespace App;

use App\Controllers\Controller;
use App\Exceptions\ValidationException;
use App\Http\Request;
use App\Http\Response;
use Exception;
use Throwable;

class App
{
    /**
     * @return void
     */
    public function run() {
        try {
            $request = new Request(
                $_SERVER['REQUEST_METHOD'],
                $_SERVER['REQUEST_URI'],
                $_POST,
                apache_request_headers()
            );

            $response = $this->initController($request);
        } catch (ValidationException $e) {
            $response = new Response([], 400, $e->getMessage());
        } catch (Exception $e) {
            $response = new Response([], $e->getCode() ?? 400, $e->getMessage());
        } catch (Throwable $e) {
            $response = new Response([], 500, "Something wrong");
        } finally {
            $response->init();
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws Exceptions\ValidationException
     * @throws Exception
     */
    public function initController(Request $request) {
        $route = $request->getRoute();

        /** @var Controller $controller */
        $controller = (new $route['controller']());

        if (method_exists($controller, $route['method']) === false) {
            throw new Exception("Метод контроллера не существует");
        }

        $controller->initValidation($request);

        return $controller->{$route['method']}();
    }

}