<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Container;

use App\Infrastructure\Container\Container;
use App\Infrastructure\Container\ContainerNotFoundException;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = new Container();
    }

    public function testSetAndGetScalarValue(): void
    {
        $this->container->set('config.debug', true);

        $this->assertTrue($this->container->get('config.debug'));
    }

    public function testSetAndGetArrayValue(): void
    {
        $config = ['host' => 'localhost', 'port' => 3306];
        $this->container->set('config.database', $config);

        $this->assertSame($config, $this->container->get('config.database'));
    }

    public function testSetAndGetObject(): void
    {
        $object = new \stdClass();
        $object->name = 'test';
        $this->container->set('my.object', $object);

        $this->assertSame($object, $this->container->get('my.object'));
    }

    public function testSetAndGetWithCallable(): void
    {
        $this->container->set('service', function () {
            return new \stdClass();
        });

        $result = $this->container->get('service');

        $this->assertInstanceOf(\stdClass::class, $result);
    }

    public function testGetReturnsCachedInstance(): void
    {
        $this->container->set('service', function () {
            return new \stdClass();
        });

        $first = $this->container->get('service');
        $second = $this->container->get('service');

        $this->assertSame($first, $second);
    }

    public function testGetThrowsExceptionForUnknownService(): void
    {
        $this->expectException(ContainerNotFoundException::class);
        $this->expectExceptionMessage('unknown.service');

        $this->container->get('unknown.service');
    }

    public function testHasReturnsTrueForExistingService(): void
    {
        $this->container->set('existing', 'value');

        $this->assertTrue($this->container->has('existing'));
    }

    public function testHasReturnsFalseForNonExistingService(): void
    {
        $this->assertFalse($this->container->has('non.existing'));
    }

    public function testSetOverwritesPreviousDefinition(): void
    {
        $this->container->set('service', 'first');
        $this->container->set('service', 'second');

        $this->assertEquals('second', $this->container->get('service'));
    }

    public function testSingletonReturnsSameInstance(): void
    {
        $this->container->singleton('service', function () {
            return new \stdClass();
        });

        $first = $this->container->get('service');
        $second = $this->container->get('service');

        $this->assertSame($first, $second);
    }

    public function testSingletonWithClassName(): void
    {
        $this->container->singleton('service', \stdClass::class);

        $first = $this->container->get('service');
        $second = $this->container->get('service');

        $this->assertSame($first, $second);
        $this->assertInstanceOf(\stdClass::class, $first);
    }

    public function testSingletonWithScalarValue(): void
    {
        $this->container->singleton('config', ['key' => 'value']);

        $first = $this->container->get('config');
        $second = $this->container->get('config');

        $this->assertSame($first, $second);
    }
}
