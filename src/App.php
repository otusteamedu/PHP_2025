<?php
declare(strict_types=1);

namespace Dinargab\Homework15;

use Dinargab\Homework15\Controller\CreateOrderController;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


class App
{

    private ContainerInterface $container;

    private CreateOrderController $createOrderController;

    public function __construct()
    {
        $this->container = new ContainerBuilder();
        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.yaml');
        $this->container->compile();
    }

    public function run()
    {
        if ($this->container->has(CreateOrderController::class)) {
            $this->createOrderController = $this->container->get(CreateOrderController::class);
        }

        $orderProducts = [
            [
                "type" => "burger",
                "additionalIngredients" => ["Cheese", "Onion", "Pepper"]
            ],
            [
                "type" => "premium burger",
                "additionalIngredients" => ["Cheese", "Onion"]
            ],
            [
                "type" => "premium sandwich",
                "additionalIngredients" => ["Onion"]
            ],
        ];
        ($this->createOrderController)($orderProducts);
    }
}