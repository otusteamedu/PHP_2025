<?php
declare(strict_types=1);


require_once dirname(__DIR__) . '/vendor/autoload.php';

use Nvanzhin\Randomizer\Random;

$random = new Random();
echo $random->randomize('Hello world');




