<?php
declare(strict_types=1);

namespace App;

use App\Http\Request;
use App\Http\Response;
use App\Services\EmailValidator;

class App
{
    public function run(): string
    {
        $request = new Request();
        $response = new Response();

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        $response->addHeader('Content-Type', 'application/json');

        if ($method !== 'POST') {
            return $response->setStatusCode(405)
                ->setContent('Method Not Allowed')
                ->send();
        }

        if ($path !== '/validate-emails') {
            return $response->setStatusCode(404)
                ->setContent('Not Found')
                ->send();
        }

        try {
            $validator = new EmailValidator();
            $emails = json_decode($request->getRawInput(), true);
            $validationResult = $validator->validate($emails);
            return $response->setStatusCode(200)
                ->setContent(json_encode($validationResult))
                ->send();
        } catch (\Exception $e) {
            return $response->setStatusCode(500)
                ->setContent('Internal Server Error. Details: ' . $e->getMessage())
                ->send();
        }
    }
}