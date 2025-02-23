<?php declare(strict_types=1);

use App\Http\Exceptions\HttpException;
use App\Http\Response;

set_exception_handler(function (Throwable $e) {
    $response = new Response();

    if ($e instanceof HttpException) {
        $name = $e->getName();
        $status = $e->getStatusCode();
    } else {
        $name = 'Internal Server Error';
        $status = 500;
    }

    $data = [
        'name' => $name,
        'status' => $status,
        'message' => $e->getMessage(),
    ];

    echo $response->send($status, $data);
});
