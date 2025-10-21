<?php

use App\Core\UserInterface\UserReport\Request;
use App\Core\UserInterface\UserReport\UserReportController;
use App\Shared\Http\Request as HttpRequest;
use App\Shared\Validate\ValidateRequestMethodPost;

require __DIR__ . '/../vendor/autoload.php';

if(ValidateRequestMethodPost::validate() !== null){
    echo ValidateRequestMethodPost::validate();
    exit();
}

$body = HttpRequest::getBody();

$userId = $body['userId'];
$interval = $body['interval'];
$cardId = $body['cardId'];
$email = $body['email'];

$request = new Request(
    userId: $userId,
    interval: $interval,
    cardId: $cardId,
    email: $email
);

new UserReportController()->generateReport($request);

echo "Запрос принят в обработку. Уведомление будет отправлено после генерации.";
exit;
