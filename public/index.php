<?php

require_once 'autoload.php';

use classes\App;

try {
    $app = new App();
    echo '<pre>';
    var_dump($app->run());
    echo '</pre>';
}
catch (Exception $e) {
     print_r($e->getMessage());
}


