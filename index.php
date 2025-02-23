<?php

require __DIR__ . '/vendor/autoload.php';

use app\EmailVerifierTest;

$verifier = new EmailVerifierTest();

$results = $verifier->runTests();