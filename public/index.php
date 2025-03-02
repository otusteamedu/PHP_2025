<?php declare(strict_types=1);

use App\App;
use App\Http\Requests\Request;
use App\Http\Response;
use App\Validations\StringVerify;

require __DIR__ . '/../vendor/autoload.php';

$request = new Request();
$response = new Response();
$validator = new StringVerify();

$app = new App($request, $response, $validator);
$app->run()->send();
