<?php
declare(strict_types=1);

namespace App;

use App\Http\Request;
use App\Http\Response;
use App\Http\Stream;
use App\Services\EmailValidator;

class App
{
    public function run(): Response
    {
        $request = Request::fromGlobals();
        $response = new Response();

        $response = $response->withHeader('Content-Type', 'application/json');

        if ($request->getMethod() !== 'POST') {
            return $response
                ->withStatus(405)
                ->withBody(new Stream('Method Not Allowed'));
        }

        if ($request->getPath() !== '/validate-emails') {
            return $response
                ->withStatus(404)
                ->withBody(new Stream('Not Found'));
        }

        try {
            $validator = new EmailValidator();
            $emails = json_decode((string)$request->getBody(), true);
            $validationResult = $validator->validate($emails);
            return $response
                ->withStatus(200)
                ->withBody(new Stream(json_encode($validationResult)));
        } catch (\Exception $e) {
            return $response
                ->withStatus(500)
                ->withBody(new Stream('Internal Server Error. Details: ' . $e->getMessage()));
        }
    }
}