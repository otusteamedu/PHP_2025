<?php

require __DIR__ . '/vendor/autoload.php';


use Alte0\OtusTestApp\ReturnText;

echo (new ReturnText('Text, text'))->getText();
