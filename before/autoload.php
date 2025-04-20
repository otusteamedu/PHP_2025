<?php

function autoLoader($class): void
{
    $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($fileName)) {
        require_once $fileName;
    }
}

spl_autoload_register('autoLoader');