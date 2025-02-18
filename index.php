<?php

require 'vendor/autoload.php';

use \Adelfenin\OtusTask\ObjectCreator;

$monkey1 = ObjectCreator::make();
echo $monkey1->eat() . "\n";
echo $monkey1->talk() . "\n";
exit;
