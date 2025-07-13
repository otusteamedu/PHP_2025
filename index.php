<?php
set_time_limit(0);
ini_set('memory_limit', '-1');
error_reporting(E_ALL & ~E_DEPRECATED);

use Elisad5791\Phpapp\OpenLibrary;

require_once 'vendor/autoload.php';

$library = new OpenLibrary();

$result = $library->getSubject('art');
print_r($result);

$result = $library->getPageCount('/works/OL8193497W');
print_r($result . PHP_EOL);