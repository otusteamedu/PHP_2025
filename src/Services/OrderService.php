<?php
namespace App\Services;

use App\Factory\BurgerFactory;
use App\Factory\SandwichFactory;
use App\Factory\HotDogFactory;
use App\Decorator\ProductDecorator;
use App\Observer\OrderStatusNotifier;
use App\Builder\OrderBuilder;
use App\ChainOfResponsibility\OrderProcessor;
use App\ChainOfResponsibility\PaymentHandler;
use App\ChainOfResponsibility\CookingHandler;
use App\ChainOfResponsibility\DeliveryHandler;

class OrderService
{
    public function createOrder()
    {
        $burgerFactory = new BurgerFactory();
        $sandwichFactory = new SandwichFactory();
        $hotDogFactory = new HotDogFactory();
        
        $decorator = new ProductDecorator();
        $statusNotifier = new OrderStatusNotifier();
        $orderBuilder = new OrderBuilder();
        
        $orderProcessor = new OrderProcessor();
        $orderProcessor->addHandler(new PaymentHandler());
        $orderProcessor->addHandler(new CookingHandler());
        $orderProcessor->addHandler(new DeliveryHandler());
        
        // Выбор продукта
        $productType = readline("Выберите продукт (1 - бургер, 2 - сэндвич, 3 - хот-дог): ");
        
        switch ($productType) {
            case '1':
                $product = $burgerFactory->createProduct();
                break;
            case '2':
                $product = $sandwichFactory->createProduct();
                break;
            case '3':
                $product = $hotDogFactory->createProduct();
                break;
            default:
                echo "Неверный выбор\n";
                return;
        }
        
        // Добавление ингредиентов
        echo "Доступные добавки: салат, лук, перец, сыр, помидор\n";
        $additives = explode(',', readline("Введите добавки через запятую (или оставьте пустым): "));
        
        foreach ($additives as $additive) {
            $additive = trim($additive);
            if (!empty($additive)) {
                $product = $decorator->addAdditive($product, $additive);
            }
        }
        
        // Создание заказа
        $orderBuilder->reset();
        $orderBuilder->setProduct($product);
        $orderBuilder->setCustomerName(readline("Ваше имя: "));
        $orderBuilder->setDeliveryAddress(readline("Адрес доставки: "));
        $order = $orderBuilder->getOrder();
        
        // Подписка на статус
        $order->attach($statusNotifier);
        
        // Обработка заказа
        $orderProcessor->processOrder($order);
        
        echo "Спасибо за заказ!\n";
    }
}
