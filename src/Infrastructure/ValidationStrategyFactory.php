<?php

namespace EmailsVerifier\Infrastructure;

use EmailsVerifier\Application\Services\CompositeValidator;
use EmailsVerifier\Domain\Interfaces\EmailValidationInterface;

class ValidationStrategyFactory
{
    public static function createStrategy(): EmailValidationInterface
    {
        $strategies = [];
        $validatorDir = __DIR__ . '/../Domain/Validators';
        $validatorFiles = glob($validatorDir . '/*.php');

        foreach ($validatorFiles as $file) {
            $className = basename($file, '.php');
            $fullClassName = 'EmailsVerifier\Domain\Validators\\' . $className;

            if (class_exists($fullClassName)
                && is_subclass_of($fullClassName, EmailValidationInterface::class)
                && $className !== 'BaseValidator') {
                if ($className === 'MxValidator') {
                    $mxChecker = new MxChecker();
                    $strategies[] = new $fullClassName($mxChecker);
                } else {
                    $strategies[] = new $fullClassName();
                }
            }
        }

        return new CompositeValidator($strategies);
    }
}
