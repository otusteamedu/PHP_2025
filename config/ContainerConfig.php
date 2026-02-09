<?php

namespace Restaurant\Config;

use DI;
use DI\Container;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Restaurant\Domain\Interfaces\CookingEventFactoryInterface;
use Restaurant\Application\Events\CookingEventFactory;
use Restaurant\Domain\Interfaces\PricingServiceInterface;
use Restaurant\Application\Services\PricingService;
use Restaurant\Domain\Interfaces\EventPublisherInterface;
use Restaurant\Domain\Builders\ProductDirector;
use Restaurant\Domain\Builders\ProductBuilderFactory;
use Restaurant\Domain\Interfaces\OrderHandlerInterface;
use Restaurant\Application\Handlers;
use Restaurant\Domain\Interfaces\SubjectInterface;
use Restaurant\Application\Events\EventManager;

class ContainerConfig
{
    private static ?Container $container = null;

    public static function getContainer(): Container
    {
        if (self::$container !== null) {
            return self::$container;
        }

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $containerBuilder = new ContainerBuilder();

        $containerBuilder->addDefinitions([
            CookingEventFactoryInterface::class => DI\factory(function () {
                $qualityThreshold = $_ENV['QUALITY_THRESHOLD'] ?? 70.0;
                return new CookingEventFactory($qualityThreshold);
            }),

            PricingServiceInterface::class => DI\create(PricingService::class),

            ProductDirector::class => DI\autowire(ProductDirector::class)
                ->constructorParameter('pricingService', DI\get(PricingServiceInterface::class)),

            ProductBuilderFactory::class => DI\autowire(ProductBuilderFactory::class)
                ->constructorParameter('pricingService', DI\get(PricingServiceInterface::class)),

            OrderHandlerInterface::class => DI\factory(function (
                Handlers\OrderCreationHandler $creationHandler,
                Handlers\OrderCookingHandler $cookingHandler,
                Handlers\OrderReadyHandler $readyHandler,
                Handlers\OrderDeliveryHandler $deliveryHandler
            ) {
                $creationHandler->setNext($cookingHandler)
                    ->setNext($readyHandler)
                    ->setNext($deliveryHandler);
                return $creationHandler;
            }),

            SubjectInterface::class => DI\autowire(EventManager::class),
            EventPublisherInterface::class => DI\get(SubjectInterface::class),
        ]);

        self::$container = $containerBuilder->build();

        return self::$container;
    }

    public static function get(string $className)
    {
        return self::getContainer()->get($className);
    }
}
