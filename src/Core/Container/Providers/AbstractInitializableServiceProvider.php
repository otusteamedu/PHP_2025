<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Container;
use App\Core\Container\Initialization\Initializers\InitializerInterface;
use App\Core\Container\Initialization\Payload\PayloadInterface;

abstract class AbstractInitializableServiceProvider implements ServiceProviderInterface
{
    protected ?InitializerInterface $initializer = null;

    abstract protected function doRegisterServices(Container $container, ?PayloadInterface $payload): void;
    abstract protected function createInitializer(Container $container): InitializerInterface;

    final public function registerServices(Container $container): void
    {
        $initializer = $this->getOrCreateInitializer($container);
        $payload = $initializer->initialize();
        $expectedType = $initializer->getExpectedPayloadType();

        $this->validatePayloadContract($payload, $expectedType, static::class);

        $this->doRegisterServices($container, $payload);
    }

    final protected function getOrCreateInitializer(Container $container): InitializerInterface
    {
        if ($this->initializer === null) {
            $this->initializer = $this->createInitializer($container);
        }

        return $this->initializer;
    }

    private function validatePayloadContract(
        ?PayloadInterface $payload,
        ?string $expectedPayloadType,
        string $providerClass,
    ): void {
        if ($expectedPayloadType === null) {
            return;
        }

        if ($payload === null) {
            throw new \RuntimeException(sprintf(
                'Configuration error in %s: initializer promised to return %s, but returned null.',
                $providerClass,
                $expectedPayloadType,
            ));
        }

        if (!$payload instanceof $expectedPayloadType) {
            throw new \LogicException(sprintf(
                'Invalid payload type in %s: expected %s, but received %s.',
                $providerClass,
                $expectedPayloadType,
                get_class($payload),
            ));
        }
    }
}
