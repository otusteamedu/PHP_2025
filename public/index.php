<?php
    require __DIR__.'/../vendor/autoload.php';

    use App\Controllers\HomeController;
    use App\Tests\TestBalance;

    $requestUri = $_SERVER['REQUEST_URI'];

    $app = new HomeController();

    if ($requestUri == '/test') {
        header("Content-Type: text/plain");
        echo $app->viewTest();
    } else {
        $result = $app->checkStr();
        http_response_code($result['status']);
        echo $result['message'];
    }

