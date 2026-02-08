<?php

declare(strict_types=1);

namespace Ak\Hw;

use Ak\Hw\Application\Service\OrderProcessingService;
use Ak\Hw\Domain\CookingStrategy\BurgerCookingStrategy;
use Ak\Hw\Domain\CookingStrategy\HotDogCookingStrategy;
use Ak\Hw\Domain\CookingStrategy\PizzaCookingStrategy;
use Ak\Hw\Domain\CookingStrategy\SandwichCookingStrategy;
use Ak\Hw\Domain\Notification\PushNotificationObserver;
use Ak\Hw\Domain\Notification\SmsNotificationObserver;
use Ak\Hw\Domain\Order\ObservableOrder;
use Ak\Hw\Domain\Template\CustomCooking;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $services = [];

    public function __construct()
    {
        $this->registerServices();
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new \Exception("Service not found: " . $id);
        }
        $service = $this->services[$id];
        return $service($this);
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }

    public function set(string $id, callable $callable): void
    {
        $this->services[$id] = $callable;
    }

    private function registerServices(): void
    {
        $this->set('Order', fn() => new ObservableOrder(random_int(1, 1000)));
        $this->set('PushNotificationObserver', fn() => new PushNotificationObserver());
        $this->set('SmsNotificationObserver', fn() => new SmsNotificationObserver());

        $this->set('BurgerCookingStrategy', fn() => new BurgerCookingStrategy());
        $this->set('SandwichCookingStrategy', fn() => new SandwichCookingStrategy());
        $this->set('HotDogCookingStrategy', fn() => new HotDogCookingStrategy());
        $this->set('PizzaCookingStrategy', fn() => new PizzaCookingStrategy());

        $this->set('CustomCooking', fn() => new CustomCooking());

        $this->set('OrderProcessingService', fn(ContainerInterface $c) => new OrderProcessingService($c));
    }
}
