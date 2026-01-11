<?php

use App\Controller\EmailValidationController;
use App\Validator\Email\EmailValidator;

require __DIR__ . '/../vendor/autoload.php';

echo new EmailValidationController(new EmailValidator())->handle();
