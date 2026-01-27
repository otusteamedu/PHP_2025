<?php

use App\App;

require $_SERVER['DOCUMENT_ROOT'] . '/init/bootstrap.php';
echo (new App)->run();
