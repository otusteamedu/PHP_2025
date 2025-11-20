<?php

declare(strict_types=1);

use League\Container\Container;
use League\Container\ReflectionContainer;
use Otus\Contracts\EmailValidatorServiceInterface;
use Otus\Services\EmailValidatorService;

$container = new Container();
$container->delegate(new ReflectionContainer());

$container
    ->add(EmailValidatorServiceInterface::class, static function (): EmailValidatorServiceInterface {
        return new EmailValidatorService();
    });

return $container;
