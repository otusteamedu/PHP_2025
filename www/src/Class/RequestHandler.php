<?php

namespace Larkinov\Myapp\Class;

use Exception;

class RequestHandler
{
    private Validator $validator;

    public function __construct()
    {
        $this->validator = new Validator();
    }

    public function handle()
    {
        try {
            $this->checkMethod();
            $this->validator->isValid();
            $this->sendResponse(200, 'success');
        } catch (\Throwable $th) {
            $this->sendResponse(400, $th->getMessage());
        }
    }

    private function checkMethod()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            throw new Exception('no valid method');
    }

    private function sendResponse(int $httpcode, string $message = "")
    {
        http_response_code($httpcode);
        echo $message;
    }
}
