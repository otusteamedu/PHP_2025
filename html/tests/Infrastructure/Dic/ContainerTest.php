<?php

declare(strict_types=1);

namespace Otus\Food\Tests\Infrastructure\Dic;

use Otus\Food\Infrastructure\Dic\Container;
use Otus\Food\Infrastructure\Dic\UnresolveParameterException;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use stdClass;

class ContainerTest extends TestCase
{
    protected function setUp(): void
    {
        $reflection = new ReflectionProperty(Container::class, 'instance');
        $reflection->setValue(null, null);
    }

    public function testGetInstance(): void
    {
        $container1 = Container::getInstance();
        $container2 = Container::getInstance();

        $this->assertInstanceOf(Container::class, $container1);
        $this->assertSame($container1, $container2);
    }

    public function testGetSimpleClass(): void
    {
        $container = Container::getInstance();
        $instance = $container->get(stdClass::class);
        $this->assertInstanceOf(stdClass::class, $instance);
    }

    public function testSingleton(): void
    {
        $container = Container::getInstance();
        $container->setSingleton(stdClass::class, function (): stdClass {
            return new stdClass();
        });

        $instance1 = $container->get(stdClass::class);
        $instance2 = $container->get(stdClass::class);

        $this->assertSame($instance1, $instance2);
    }

    public function testDefinition(): void
    {
        $container = Container::getInstance();
        $container->setDefinition(stdClass::class, function (): stdClass {
            return new stdClass();
        });

        $instance1 = $container->get(stdClass::class);
        $instance2 = $container->get(stdClass::class);

        $this->assertNotSame($instance1, $instance2);
    }

    public function testResolveWithDependencies(): void
    {
        $container = Container::getInstance();
        $instance = $container->get(TestClassNoDeps::class);
        $this->assertInstanceOf(TestClassNoDeps::class, $instance);
    }

    public function testResolveWithRecursiveDependencies(): void
    {
        $container = Container::getInstance();
        $instance = $container->get(TestClassWithDeps::class);

        $this->assertInstanceOf(TestClassWithDeps::class, $instance);
        $this->assertInstanceOf(TestClassNoDeps::class, $instance->dep);
    }

    public function testUnresolveParameterException(): void
    {
        $this->expectException(UnresolveParameterException::class);
        $container = Container::getInstance();
        $container->get(TestClassWithBuiltinDep::class);
    }
}

class TestClassNoDeps
{
}

class TestClassWithDeps
{
    public function __construct(public TestClassNoDeps $dep)
    {
    }
}

class TestClassWithBuiltinDep
{
    public function __construct(
        public string $version {
            get {
                return $this->version;
            }
        }
    )
    {
    }
}
