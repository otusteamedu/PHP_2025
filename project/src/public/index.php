<?php

define("APP_DIR", dirname(__DIR__) . '/app');

require_once APP_DIR.'/app.php';
require_once APP_DIR.'/validator.php';
require_once APP_DIR.'/view.php';

// Создаем экземпляр приложения и запускаем его
$app = new App();
echo $app->run();
