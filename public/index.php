<?php

require_once 'autoload.php';


use classes\App;
try {
   $app = new App();
   $app->run();
}

catch (Exception $e) {
    print_r($e->getMessage());
}