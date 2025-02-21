<?php
    require __DIR__.'/../vendor/autoload.php';

    use App\Router;
    use App\Http\Request;

    $request = new Request();

    $response = Router::dispatch($request);

    // Отправляем HTTP-заголовки и код состояния
    http_response_code($response->getStatusCode());
    foreach ($response->getHeaders() as $key => $value) {
        header("$key: $value");
    }

    // Выводим ответ в нужном формате
    echo $response->getBody();
