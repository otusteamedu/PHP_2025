<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\App;
use App\Service\Validator;
use App\Response\Response;
use App\Service\EmptyValidator;
use App\Service\BracketValidator;


$app = new App(new Validator([
	new EmptyValidator(),
	new BracketValidator()
]), new Response());
echo $app->run();
