<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/bootstrap.php';

use App\Classes\QueueManager;
use App\Classes\JobProcessor;

//подключаемся к очереди и начинает слушать задачи
//как только получаем -> передаем в JobProcessor
QueueManager::consume(function ($data) {
    (new JobProcessor())->handle($data);
});
