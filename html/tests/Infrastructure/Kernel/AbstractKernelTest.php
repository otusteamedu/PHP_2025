<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Infrastructure\Kernel;

use Otus\Queue\Infrastructure\Dic\Container;
use Otus\Queue\Infrastructure\Kernel\AbstractKernel;
use PHPUnit\Framework\TestCase;

final class AbstractKernelTest extends TestCase
{
    protected function setUp(): void
    {
        $reflection = new \ReflectionClass(Container::class);
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null);
    }

    public function testConstructorRegistersSingletonsAndDefinitionsInContainer(): void
    {
        new class ([
            'container' => [
                'singletons' => [
                    KernelSingleton::class => static fn (): KernelSingleton => new KernelSingleton(),
                ],
                'definitions' => [
                    KernelDefinition::class => static fn (): KernelDefinition => new KernelDefinition(),
                ],
            ],
        ]) extends AbstractKernel {
        };

        $container = Container::getInstance();

        self::assertInstanceOf(KernelSingleton::class, $container->get(KernelSingleton::class));
        self::assertInstanceOf(KernelDefinition::class, $container->get(KernelDefinition::class));
    }
}

final class KernelSingleton
{
}

final class KernelDefinition
{
}
