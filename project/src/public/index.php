<?php

define("APP_DIR", dirname(__DIR__) . '/app');

require_once APP_DIR.'/app.php';

// Создаем экземпляр приложения и запускаем его
$app = new App();
echo $app->run();
