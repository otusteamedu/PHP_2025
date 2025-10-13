<?php

declare(strict_types=1);

namespace App\Tests\Module;

use Codeception\Module;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;

final class Reflection extends Module
{
    /**
     * @throws ReflectionException
     */
    public function setReflectionProperty(object $obj, string $name, mixed $value): void
    {
        $reflection = new ReflectionObject($obj);

        $property = $reflection->getProperty($name);

        $property->setValue($obj, $value);
    }

    /**
     * @throws ReflectionException
     */
    public function getReflectionProperty(object $obj, string $property): mixed
    {
        $reflectionClass = new ReflectionClass($obj);

        return $reflectionClass
            ->getProperty($property)
            ->getValue($obj);
    }
}
