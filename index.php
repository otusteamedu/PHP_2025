<?php

require_once "vendor/autoload.php";

function dump(...$args)
{
    echo "<pre>";
    var_dump(...$args);
    echo "</pre>";
    die();
}

function dd(...$args)
{
    dump(...$args);
    die();
}

include "./App/MyApp.php";
$app = new App\MyApp();
$app->process();
