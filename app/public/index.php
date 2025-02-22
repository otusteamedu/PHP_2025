<?php

try {
    $postParam = 'string';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Request method must be POST.');
    }
    $requestBody = file_get_contents('php://input');
    preg_match("/$postParam=((?>[^()]|\((?1)\))*)$/m", $requestBody, $matches);
    if (!current($matches)) {
        throw new Exception(sprintf('Request body must have the correct format, for example `%s=()()()()`.', $postParam));
    }
    if (current($matches) === $postParam . '=') {
        throw new Exception(sprintf('Parameter `%s` has no value.', $postParam));
    }
    http_response_code(200);

    echo 'Request body has the correct format.' . PHP_EOL . 'Container name: ' . $_SERVER['HOSTNAME'];

} catch (Throwable $e) {
    http_response_code(400);

    echo $e->getMessage() . PHP_EOL . 'Container name: ' . $_SERVER['HOSTNAME'];
}