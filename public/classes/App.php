<?php declare(strict_types=1);

namespace classes;

class App
{
    public function run()
    {
        $stringHandler = new StringHandler();
        return $stringHandler->handleString($_POST['string']);
    }
}