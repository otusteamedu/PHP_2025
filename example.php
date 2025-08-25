<?php


declare(strict_types=1);

use Dkeruntu\ComposerPackageIfFive\IfFive;

require __DIR__ . '/vendor/autoload.php';


$arResult = new IfFive();

var_dump($arResult->checkFive(5));
