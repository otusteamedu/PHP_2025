<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

/**
 * Class BaseController
 * @package App\Infrastructure\Http\Controller
 */
class BaseController
{
    /**
     * @var Request
     */
    private Request $request;
    /**
     * @var Response
     */
    private Response $response;

    /**
     *
     */
    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param mixed $data
     * @return Response
     */
    public function asJson(mixed $data): Response
    {
        $this->response->setFormat(Response::FORMAT_JSON);
        $this->response->setData($data);

        return $this->response;
    }
}
