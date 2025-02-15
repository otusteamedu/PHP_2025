<?php 

require_once('../vendor/autoload.php');

use Pavelklimenko\OtusTestPackage\Functions\TestFunctionalClass;

$pavelClass = new TestFunctionalClass();

$sum = $pavelClass->calculateSum(3, 5);

echo $sum; 

?>
