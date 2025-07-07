<?php

namespace Infrastructure\Tasks;

use Application\Http\Controller\Controller;
use Application\Http\Request;
use Application\Http\Response;
use Application\Tasks\Task;
use Exception;
use Infrastructure\Exceptions\ValidationException;

class InitControllerTask extends Task
{
    /**
     * @param Request $request
     * @return Response
     * @throws ValidationException
     * @throws Exception
     */
    public function run(Request $request): Response {
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