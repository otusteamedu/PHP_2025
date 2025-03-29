<?php

use classes\MongoDBService;
use classes\RedisDBService;

require_once '../vendor/autoload.php';
require_once 'autoload.php';

//TODO АПИ ДОДЕЛАТЬ!!!!


use classes\App;
try {
   $app = new App();
   $app->run();
}

catch (Exception $e) {
    print_r($e->getMessage());
}