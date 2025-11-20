<?php

declare(strict_types=1);

use League\Container\ReflectionContainer;
use Otus\Contracts\EmailValidatorServiceInterface;
use Otus\Kernel\Application;
use Otus\Services\EmailValidatorService;

Application::getInstance()
    ->container
    ->delegate(new ReflectionContainer());

Application::getInstance()
    ->container
    ->add(EmailValidatorServiceInterface::class, static function (): EmailValidatorServiceInterface {
        return new EmailValidatorService();
    });
