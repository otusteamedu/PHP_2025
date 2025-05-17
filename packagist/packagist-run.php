<?php

// Подключаем автозагрузку Composer'а
require_once 'vendor/autoload.php';

use VMatkovskii\VideoDl\VideoDownloader;

// Создаём объект загрузчика видео
$downloader = new VideoDownloader();

// Проверяем, есть ли URL в аргументах командной строки
$url = $argv[1] ?? null;

if (empty($url)) {
    fwrite(STDERR, "Ошибка: не передан URL для загрузки\n");
    exit(1);
}

// Запускаем загрузку, передавая аргументы вручную
$downloader->run([$argv[0], $url]);
