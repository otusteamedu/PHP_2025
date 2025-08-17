<?php

use Larkinov\HowAreYou\HowAreYou;

require_once __DIR__ . '/vendor/autoload.php';

$howAreYou = new HowAreYou();

echo "Ivan: {$howAreYou->say()}\n";
echo "Roman: {$howAreYou->sayDetail()}\n";
