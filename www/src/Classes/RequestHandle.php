<?php

namespace Larkinov\Myapp\Classes;

use Exception;
use Larkinov\Myapp\Services\EmailValidation;

class RequestHandle
{

    private EmailValidation $validator;

    public function __construct()
    {
        $this->validator = new EmailValidation();
    }

    public function handle(): void
    {
        try {
            $this->checkMethod();
            $this->validator->isValid();
            $this->sendResponse(200,'this is a valid email');
        } catch (\Throwable $th) {
            $this->sendResponse(400, $th->getMessage());
        }
    }

    private function checkMethod(): void
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
