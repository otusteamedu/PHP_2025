<?php
declare(strict_types=1);

require_once __DIR__ . '/composer/vendor/autoload.php';

use Zibrov\OtusPhp2025Hw4\Numbers;
use ZibrovOleg\OtusPhp2025Hw4\Function\Maths;

$number = (new Numbers)?->getRandom();
if ($number) {
    if (class_exists('ZibrovOleg\OtusPhp2025Hw4\Function\Maths')) {
        echo 'Fibonacci number "' . $number . '":' . PHP_EOL;
        echo Maths::getFibonacci($number) . PHP_EOL;
    } else {
        echo 'Library "zibrov-oleg/otus-php-2025-hw4" is not connected';
    }
}
