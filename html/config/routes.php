<?php

declare(strict_types=1);

use Laminas\Diactoros\ResponseFactory;
use League\Container\Container;
use League\Route\Router;
use League\Route\Strategy\JsonStrategy;
use Middlewares\BasicAuthentication;
use Otus\Http\Controllers\Index;
use Otus\Http\Controllers\EmailValidator;

/** @var Container $container */

// use Middlewares\ContentLength;
// use Middlewares\JsonPayload;
// use Middlewares\XmlPayload;

$strategy = new JsonStrategy(new ResponseFactory());
$strategy->setContainer($container);

$router = new Router();
$router->setStrategy($strategy);

if (getenv('APPLICATION_USERNAME') && getenv('APPLICATION_PASSWORD')) {
    $router->middleware(new BasicAuthentication([
        getenv('APPLICATION_USERNAME') => getenv('APPLICATION_PASSWORD'),
    ]));
}

// $router->middleware(new JsonPayload());
// $router->middleware(new XmlPayload());
// $router->middleware(new ContentLength());

$router->get('/', Index::class);
$router->get('/email/validator', EmailValidator::class);

return $router;
