<?php

const $appDir = dirname(__DIR__).'/app';

require_once $appDir.'/app.php';
require_once $appDir.'/validator.php';
require_once $appDir.'/view.php';

// Создаем экземпляр приложения и запускаем его
$app = new App();
echo $app->run();
