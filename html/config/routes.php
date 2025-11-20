<?php

declare(strict_types=1);

use Middlewares\BasicAuthentication;
use Otus\Http\Controllers\EmailValidator;
use Otus\Http\Controllers\Index;
use Otus\Kernel\Application;

// use Middlewares\ContentLength;
// use Middlewares\JsonPayload;
// use Middlewares\XmlPayload;

if (getenv('APPLICATION_USERNAME') && getenv('APPLICATION_PASSWORD')) {
    Application::getInstance()
        ->router
        ->middleware(new BasicAuthentication([
            getenv('APPLICATION_USERNAME') => getenv('APPLICATION_PASSWORD'),
        ]));
}

// $router->middleware(new JsonPayload());
// $router->middleware(new XmlPayload());
// $router->middleware(new ContentLength());

Application::getInstance()->router->get('/', Index::class);
Application::getInstance()->router->get('/email/validator', EmailValidator::class);
