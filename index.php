<?php

declare(strict_types=1);

require 'vendor/autoload.php';

$greeter = new \Dinargab\Otusapp\Service\WorldGreeter();
$greeter->greet();