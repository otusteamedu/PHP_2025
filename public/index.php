<?php
require_once '../vendor/autoload.php';

use App\Classes\App;

try {
    $app = new App();
    $app->run();
}

catch (Exception $e) {
    print_r($e->getMessage());
}

?>