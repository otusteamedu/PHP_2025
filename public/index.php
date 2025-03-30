<?php

require_once '../vendor/autoload.php';
require_once 'autoload.php';


use classes\App;
try {
   $app = new App();
   echo $app->run();
}

catch (Exception $e) {
    print_r($e->getMessage());
}