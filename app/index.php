<?php

require './vendor/autoload.php';

use App\Exception\HumanReadableException;
use App\Http\Response;
use App\Validator\Validator;
use App\Http\Request;
$request = new Request();

$validator = new Validator();

$statusCode = 200;
$message = 'Строка валидна';

try {
    if (!$validator->validate($request->getPostParam('string'))) {
        $message = 'Значение не валидно';
        $statusCode = 400;
    }
} catch (HumanReadableException $humanReadableException) {
    $message = $humanReadableException->getHumanReadableException();
    $statusCode = 400;
} catch (Throwable $throwable) {
    $message = 'Что-то пошло не так';
    $statusCode = 500;
}

$response = new Response($statusCode, $message);
$response->send();