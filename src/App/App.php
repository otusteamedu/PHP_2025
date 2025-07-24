<?php
declare(strict_types=1);

namespace App;

use App\Exceptions\EmptyStringException;
use App\Exceptions\InvalidBracketsException;
use App\Http\Request;
use App\Http\Response;
use App\Services\BracketsValidator;

class App
{
    public function run(): string
    {
        $request = new Request();
        $response = new Response();
        $validator = new BracketsValidator();

        try {
            $response->addHeader('Content-Type', 'text/plain');

            if ($request->getMethod() !== 'POST') {
                return $response->setStatusCode(405)
                    ->setContent('Method Not Allowed')
                    ->send();
            }

            $inputString = (string)$request->getPostParam('string', '');
            $isValid = $validator->validate($inputString);

            if (!$isValid) {
                return $response->setStatusCode(400)
                    ->setContent('Failed: The brackets are not balanced')
                    ->send();
            }

            return $response->setStatusCode(200)
                ->setContent('OK: The brackets are balanced')
                ->send();

        } catch (EmptyStringException|InvalidBracketsException $e) {
            return $response->setStatusCode(400)
                ->setContent('Bad Request: ' . $e->getMessage())
                ->send();
        } catch (\Throwable $e) {
            return $response->setStatusCode(500)
                ->setContent('Internal Server Error')
                ->send();
        }
    }
}