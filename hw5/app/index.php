<?php

require __DIR__ . '/../vendor/autoload.php';

use Validation\Email\ValidatorEmail;

$emails = ['test@gmail.com', 'invalid-email', 'admin@non-existent-domain.xyz'];

$validator = new ValidatorEmail();
$result = $validator->isValid($emails);

print_r($result);