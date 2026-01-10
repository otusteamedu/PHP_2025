<?php

namespace App\Tasks;

use App\Controllers\Controller;
use App\Exceptions\ValidationException;
use App\Http\Request;
use Exception;

class InitControllerTask extends Task
{
    /**
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     * @throws Exception
     */
    public function run(Request $request) {
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