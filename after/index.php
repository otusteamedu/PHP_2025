<?php

require __DIR__ . '/../src/Core/Kernel.php';

use Core\Kernel;

$kernel = new Kernel();
echo $kernel->handle();