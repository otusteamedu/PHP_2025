<?php

use Trijin\OtusEmailValidator\Validator;

require "../vendor/autoload.php";

$validator = new Validator();
var_dump($validator->isValid('trijin@gmail.com'));
var_dump($validator->isValid('trijin+otus@gmail.com'));
var_dump($validator->isValid('trij@in@gmail.com'));
