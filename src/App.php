<?php declare(strict_types=1);

namespace App;

use App\Exceptions\HttpException;
use App\Exceptions\MethodNotAllowedException;
use App\Exceptions\ValidationException;
use App\Http\Request;
use App\Http\Response;

class App
{
    private Request $request;
    private Response $response;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
    }

    public function run(): Response
    {
        try {
            if ($this->request->getMethod() !== 'POST') {
                throw new MethodNotAllowedException('POST method not allowed');
            }

            $str = $this->request->getPostValue('string');

            if (empty($str)) {
                throw new ValidationException('String is empty or does not exist');
            }

            if ($this->isValid($str)) {
                return $this->response->send(200, [
                    'message' => 'String is correct',
                ]);
            } else {
                throw new ValidationException('String is invalid');
            }
        } catch (HttpException $e) {
            return $this->response->send($e->getHttpCode(), [
                'message' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            return $this->response->send(500, [
                'message' => 'Internal server error',
            ]);
        }
    }

    private function isValid(string $str): bool
    {
        $stack = [];

        foreach (str_split($str) as $char) {
            if ($char === '(') {
                $stack[] = $char;
            } else if ($char === ')') {
                if (empty($stack)) {
                    return false;
                }
                array_pop($stack);
            } else {
                return false;
            }
        }

        return empty($stack);
    }
}
