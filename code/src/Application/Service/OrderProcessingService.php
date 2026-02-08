<?php

declare(strict_types=1);

namespace Ak\Hw\Application\Service;

use Ak\Hw\Application\Factory\CookingEventFactory;
use Ak\Hw\Domain\Composite\ComboProduct;
use Ak\Hw\Domain\Composite\SingleProduct;
use Psr\Container\ContainerInterface;

class OrderProcessingService
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function processOrder(array $productNames, array $customIngredients, string $notificationType): array
    {
        $order = $this->container->get('Order');
        $observer = $this->container->get($notificationType . 'NotificationObserver');
        $order->addObserver($observer);

        $messages = [];
        $cookedProducts = [];

        foreach ($productNames as $productName) {
            $preEvent = CookingEventFactory::createEvent('pre');
            $messages[] = $preEvent->handle($productName);

            if (empty($customIngredients)) {
                $cookingStrategy = $this->container->get($productName . 'CookingStrategy');
                $cookedProduct = $cookingStrategy->cook($productName);
            } else {
                $cookingProcess = $this->container->get('CustomCooking');
                $cookedProduct = $cookingProcess->cook($productName, $customIngredients);
            }
            $cookedProducts[] = $cookedProduct;

            $postEvent = CookingEventFactory::createEvent('post');
            $messages[] = $postEvent->handle($cookedProduct);
        }

        $productComponent = null;
        if (count($cookedProducts) > 1) {
            $combo = new ComboProduct('Комбо');
            foreach ($cookedProducts as $product) {
                $combo->add(new SingleProduct($product, 10.0));
            }
            $productComponent = $combo;
        } elseif (count($cookedProducts) === 1) {
            $productComponent = new SingleProduct($cookedProducts[0], 10.0);
        }

        if ($productComponent) {
            $order->setStatus('preparing');
            $statusMessages = $productComponent->moveStatus('preparing');
            $messages = array_merge($messages, (array)$statusMessages);

            $order->setStatus('ready');
            $statusMessages = $productComponent->moveStatus('ready');
            $messages = array_merge($messages, (array)$statusMessages);
        }

        return [
            'messages' => $messages,
            'finalProduct' => $productComponent ? implode(', ', $cookedProducts) : '',
            'totalPrice' => $productComponent ? $productComponent->getPrice() : 0
        ];
    }
}
