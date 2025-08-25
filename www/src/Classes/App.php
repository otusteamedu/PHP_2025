<?php

namespace Larkinov\Myapp\Classes;

class App
{
    private RequestHandle $requestHandle;

    public function __construct()
    {
        $this->requestHandle = new RequestHandle();
    }

    public function run(): void
    {
        $this->requestHandle->handle();
    }
}
