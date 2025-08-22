<?php

namespace Larkinov\Myapp\Class;

class App
{
    public function run(): string
    {
        try {
            auth();
            validate();

            header('HTTP/1.1 200');
            return 'success';
            
        } catch (\Throwable $th) {
            header('HTTP/1.1 400 Bad Request');
            return $th->getMessage();
        }
    }
}
