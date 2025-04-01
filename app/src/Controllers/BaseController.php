<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Response;

/**
 * Class BaseController
 * @package App\Controllers
 */
class BaseController
{
    /**
     * @var Response
     */
    private Response $response;

    /**
     *
     */
    public function __construct()
    {
        $this->response = new Response();
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
