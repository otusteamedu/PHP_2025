<?php
declare(strict_types=1);

require_once __DIR__ . '/composer/vendor/autoload.php';

use Zibrov\OtusPhp2025Hw4\Numbers;
use ZibrovOleg\OtusPhp2025Hw4\Maths;

$obNumbers = new Numbers();
try {
    $number = $obNumbers->getRandom();
} catch (Exception $e) {
    echo 'Error numbers: ' . $e->getMessage();
}

if (!empty($number)) {
    if (class_exists(Maths::class)) {
        echo 'Fibonacci number "' . $number . '":' . PHP_EOL;
        echo Maths::getFibonacci($number) . PHP_EOL;
    } else {
        echo 'Library "zibrov-oleg/otus-php-2025-hw4" is not connected';
    }
}
