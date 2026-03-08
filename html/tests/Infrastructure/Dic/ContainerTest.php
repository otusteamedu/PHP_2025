<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Infrastructure\Dic;

use Otus\Queue\Infrastructure\Dic\Container;
use Otus\Queue\Infrastructure\Dic\UnresolveParameterException;
use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{
    protected function setUp(): void
    {
        $reflection = new \ReflectionClass(Container::class);
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null);
    }

    public function testGetReturnsSingletonInstance(): void
    {
        $container = Container::getInstance();
        $container->setSingleton(TestSingleton::class, static fn (): TestSingleton => new TestSingleton());

        $first = $container->get(TestSingleton::class);
        $second = $container->get(TestSingleton::class);

        self::assertSame($first, $second);
    }

    public function testGetReturnsDefinitionWithoutCaching(): void
    {
        $container = Container::getInstance();
        $container->setDefinition(TestDefinition::class, static fn (): TestDefinition => new TestDefinition());

        $first = $container->get(TestDefinition::class);
        $second = $container->get(TestDefinition::class);

        self::assertNotSame($first, $second);
    }

    public function testResolveBuildsObjectGraph(): void
    {
        $container = Container::getInstance();

        $resolved = $container->get(TestWithDependency::class);

        self::assertInstanceOf(TestWithDependency::class, $resolved);
        self::assertInstanceOf(TestDependency::class, $resolved->dependency);
        self::assertSame('default', $resolved->mode);
    }

    public function testResolveThrowsForScalarWithoutDefault(): void
    {
        $container = Container::getInstance();

        $this->expectException(UnresolveParameterException::class);

        $container->get(TestWithScalar::class);
    }
}

final class TestSingleton
{
}

final class TestDefinition
{
}

final class TestDependency
{
}

final class TestWithDependency
{
    public function __construct(public TestDependency $dependency, public string $mode = 'default')
    {
    }
}

final class TestWithScalar
{
    public function __construct(string $value)
    {
    }
}
