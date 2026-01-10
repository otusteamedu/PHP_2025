<?php

declare(strict_types=1);

namespace App;

use App\Controllers\MovieController;
use App\Http\Request;
use App\Http\Response;

class App
{
    private Request $request;
    private Response $response;
    private MovieController $movieController;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->movieController = new MovieController($this->request, $this->response);
    }

    public function run(): string
    {
        $path = $this->request->getPath();

        if ($path === '/movies' && $this->request->isGet()) {
            return $this->movieController->getAll();
        }

        if (preg_match('#^/movies/(\d+)$#', $path, $matches) === 1) {
            $id = (int)$matches[1];

            if ($this->request->isGet()) {
                return $this->movieController->getOne($id);
            }

            if ($this->request->isDelete()) {
                return $this->movieController->delete($id);
            }
        }

        if ($path === '/movies' && $this->request->isPost()) {
            return $this->movieController->create();
        }

        return $this->response->error(404, 'Маршрут не найден. Доступно: GET /movies, GET /movies/{id}, POST /movies, DELETE /movies/{id}.');
    }
}
