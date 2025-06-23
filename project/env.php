<?php

use Sobol\PhpTypedEnv\TypedEnv;
use Sobol\PhpTypedEnv\TypedEnvException;

require __DIR__ . '/vendor/autoload.php';

function getAppName(): string
{
    try {
        $typedEnv = new TypedEnv();
        return $typedEnv->getString('APP_NAME');
    } catch (TypedEnvException $e) {
        echo $e->getMessage();
    }

    return '';
}

function getAppDebugMode(): bool
{
    try {
        $typedEnv = new TypedEnv();
        return $typedEnv->getBool('APP_DEBUG');
    } catch (TypedEnvException $e) {
        echo $e->getMessage();
    }

    return false;
}

function getUserMarks(): array
{
    try {
        $typedEnv = new TypedEnv();
        return $typedEnv->getArray('USER_MARKS');
    } catch (TypedEnvException $e) {
        echo $e->getMessage();
    }

    return [];
}


