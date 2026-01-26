<?php

declare(strict_types=1);

namespace Otus\DataMapper\Infrastructure\Dic;

use Exception;
use ReflectionParameter;

class UnresolveParameterException extends Exception
{
    /**
     * @param ReflectionParameter $parameter
     */
    public function __construct(ReflectionParameter $parameter)
    {
        $message = sprintf(
            'Unresolvable dependency %s in class %s',
            $parameter->getName(),
            $parameter->getDeclaringClass()->getName(),
        );

        parent::__construct($message);
    }
}
