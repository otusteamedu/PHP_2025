<?php

declare(strict_types=1);

require_once __DIR__ . '/../App.php';

use Dkeruntu\OtusHomeWorkFiveCheckMail\App;

$obApp = new App();
echo $obApp->run();