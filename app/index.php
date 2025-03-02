<?php

require './vendor/autoload.php';

use App\Exception\HumanReadableException;
use App\Http\Response;
use App\Service\EmailSenderService;
use App\Validator\Validator;
use App\Http\Request;
$request = new Request();

$validator = new Validator();

$statusCode = 200;
$message = '';

try {
    $invalidEmails = [];
    $validEmails = [];
    foreach ($request->getPostParam('emails') as $email) {
        if (!$validator->validateEmail($email)) {
            $invalidEmails[] = $email;
        } else {
            $validEmails[] = $email;
        }
    }

    $message = count($invalidEmails) > 0
        ? 'Невалидные email адреса: ' . implode(',', $invalidEmails)
        : 'Все переданные адреса валидны';

    if (count($validEmails) > 0) {
        $sender = new EmailSenderService();
        $sender->sendOut($validEmails);
    }
} catch (Throwable $throwable) {
    $message = 'Что-то пошло не так';
    $statusCode = 500;
}

$response = new Response($statusCode, $message);
$response->send();