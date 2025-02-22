
<?php

require __DIR__ . '/vendor/autoload.php';

use app\EmailVerifierTest;

$verifier = new EmailVerifierTest();

$results = $verifier->runTests();

foreach ($results as $email => $result) {
    echo "$email: ";
    echo $result['valid_format'] ? "Valid format, " : "Invalid format, ";
    echo $result['has_mx_records'] ? "Has MX records" : "Does not have MX records";
    echo '<br>';
}
