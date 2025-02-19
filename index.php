<?php

require_once __DIR__ . '/vendor/autoload.php';

use Src\Counter;

$counter = new Counter(10);
$counter->increment();
echo $counter->getCounterValue();