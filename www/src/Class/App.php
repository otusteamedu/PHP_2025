<?php

namespace Larkinov\Myapp\Class;

class App
{

    private RequestHandler $requestHandler;

    public function __construct()
    {
        $this->requestHandler = new RequestHandler();
    }

    public function run()
    {
        return $this->requestHandler->handle();
    }
}
