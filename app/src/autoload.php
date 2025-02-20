<?php declare(strict_types=1);

spl_autoload_register(function (string $class) {
    $class = str_replace('App\\', 'src\\', $class);
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    $filePath = "../$class.php";
    if (file_exists($filePath)) {
        require($filePath);
    }
});
