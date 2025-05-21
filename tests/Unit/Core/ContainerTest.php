<?php

namespace Tests\Unit\Core;

use App\Core\Container;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use RuntimeException;

class ContainerTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = new Container();
    }

    /**
     * @throws ReflectionException
     */
    public function testBindAndResolve()
    {
        $this->container->bind('test', fn() => 'test_value');
        $this->assertEquals('test_value', $this->container->make('test'));
    }

    /**
     * @throws ReflectionException
     */
    public function testSingleton()
    {
        $this->container->singleton('singleton', fn() => new \stdClass());
        $first = $this->container->make('singleton');
        $second = $this->container->make('singleton');
        $this->assertSame($first, $second);
    }

    /**
     * @throws ReflectionException
     */
    public function testClassResolution()
    {
        $object = $this->container->make(self::class);
        $this->assertInstanceOf(self::class, $object);
    }

    /**
     * @throws ReflectionException
     */
    public function testNonexistentClass()
    {
        $this->expectException(RuntimeException::class);
        $this->container->make('NonexistentClass');
    }

    /**
     * @throws ReflectionException
     */
    public function testUninstantiableClass()
    {
        $this->expectException(RuntimeException::class);
        $this->container->make(AbstractClass::class);
    }
}

abstract class AbstractClass {}