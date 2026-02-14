<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\DTO\CreateOrderRequest;
use App\Application\DTO\OrderResponse;
use App\Domain\Interfaces\CreateOrderUseCaseInterface;
use App\Domain\Cooking\Factory\CookingProcessFactory;
use App\Infrastructure\Notifications\PushNotificationObserver;
use App\Infrastructure\Notifications\SmsNotificationObserver;
use App\Domain\Interfaces\OrderSubjectInterface;
use App\Domain\Interfaces\OrderBuilderInterface;
use App\Domain\Product\Decorator\CheeseDecorator;
use App\Domain\Product\Decorator\LettuceDecorator;
use App\Domain\Product\Decorator\OnionDecorator;
use App\Domain\Product\Decorator\PepperDecorator;
use App\Domain\Product\Decorator\TomatoDecorator;
use App\Domain\Product\Factory\ProductFactory;
use App\Domain\Interfaces\ProductInterface;
use InvalidArgumentException;

class CreateOrderUseCase implements CreateOrderUseCaseInterface
{
    private const ADDITION_DECORATORS = [
        'lettuce' => LettuceDecorator::class,
        'onion' => OnionDecorator::class,
        'pepper' => PepperDecorator::class,
        'cheese' => CheeseDecorator::class,
        'tomato' => TomatoDecorator::class,
    ];

    public function __construct(
        private ProductFactory $productFactory,
        private OrderBuilderInterface $orderBuilder,
        private OrderSubjectInterface $orderSubject,
        private CookingProcessFactory $cookingProcessFactory,
        private PushNotificationObserver $pushObserver,
        private SmsNotificationObserver $smsObserver
    ) {
        $this->orderSubject->attach($this->pushObserver);
        $this->orderSubject->attach($this->smsObserver);
    }

    public function execute(CreateOrderRequest $request): OrderResponse
    {
        try {
            if (empty($request->productType)) {
                return OrderResponse::error('Тип продукта не указан');
            }

            $product = $this->productFactory->createProduct($request->productType);

            $product = $this->productFactory->createProduct($request->productType);

            $product = $this->applyAdditions($product, $request->additions);

            $order = $this->orderBuilder
                ->create()
                ->addProduct($product)
                ->build();

            $this->orderSubject->setOrder($order);
            $this->orderSubject->notify('Заказ создан');

            $cookingProcess = $this->cookingProcessFactory->createProcess($request->productType);
            $cookingResult = $cookingProcess->cook($order);

            $notifications = array_merge(
                $this->pushObserver->getSentNotifications(),
                $this->smsObserver->getSentNotifications()
            );

            if ($cookingResult->success) {
                return OrderResponse::success(
                    order: $cookingResult->order,
                    cookingLogs: $cookingResult->logs,
                    notifications: $notifications
                );
            }

            return OrderResponse::error(
                message: $cookingResult->message,
                order: $cookingResult->order,
                cookingLogs: $cookingResult->logs,
                notifications: $notifications
            );
        } catch (InvalidArgumentException $e) {
            return OrderResponse::error($e->getMessage());
        } catch (\Throwable $e) {
            return OrderResponse::error('Произошла ошибка: ' . $e->getMessage());
        }
    }

    private function applyAdditions(ProductInterface $product, array $additions): ProductInterface
    {
        foreach ($additions as $addition) {
            $addition = strtolower(trim($addition));

            if (isset(self::ADDITION_DECORATORS[$addition])) {
                $decoratorClass = self::ADDITION_DECORATORS[$addition];
                $product = new $decoratorClass($product);
            }
        }

        return $product;
    }
}
