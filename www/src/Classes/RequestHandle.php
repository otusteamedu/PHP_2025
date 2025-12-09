<?php

namespace Larkinov\Myapp\Classes;

use Larkinov\Myapp\Services\Validator;

class RequestHandle
{

    private Validator $validator;

    public function __construct()
    {
        $this->validator = new Validator();
    }

    public function handle(): void
    {
        try {
            $this->validator->validateRequest();
            $this->validator->validateEmail();
            $this->sendResponse(200,'this is a valid email');
        } catch (\Throwable $th) {
            $this->sendResponse(400, $th->getMessage());
        }
    }

    private function sendResponse(int $httpcode, string $message = "")
    {
        http_response_code($httpcode);
        echo $message;
    }
}
