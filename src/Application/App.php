<?php
declare(strict_types=1);

namespace App\Application;

use App\Application\Services\EmailValidator;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Infrastructure\Http\Stream;

class App
{

    private Response $response;
    private Request $request;

    public function __construct()
    {
        $response = new Response();
        $this->response = $response->withHeader('Content-Type', 'application/json');

        $this->request = Request::fromGlobals();
    }

    public function run(): Response
    {
        if ($this->request->getMethod() !== 'POST') {
            return $this->response->withStatus(405)->withBody(new Stream('Method Not Allowed'));
        }

        if ($this->request->getPath() !== '/validate/emails') {
            return $this->response->withStatus(404)->withBody(new Stream('Not Found'));
        }

        try {
            $validator = new EmailValidator();
            $arEmails = json_decode((string)$this->request->getBody(), true, 512, JSON_THROW_ON_ERROR);
            $arValidationResult = $validator->validate($arEmails);

            return $this->response
                ->withStatus(200)
                ->withBody(new Stream(json_encode($arValidationResult, JSON_THROW_ON_ERROR)));
        } catch (\Exception $e) {
            return $this->response
                ->withStatus(500)
                ->withBody(new Stream('Internal Server Error. Details: ' . $e->getMessage()));
        }
    }
}