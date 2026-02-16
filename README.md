# HW15

**Частичный функционал интернет-ресторана по продаже фаст-фуда.**

1. Абстрактная фабрика будет отвечать за генерацию базового продукта-прототипа: бургер, сэндвич или хот-дог
2. При готовке каждого типа продукта Декоратор будет добавлять составляющие к базовому продукту либо по рецепту, либо по пожеланию клиента (салат, лук, перец и т.д.)
3. Наблюдатель подписывается на статус приготовления и отправляет оповещения о том, что изменился статус приготовления продукта.
4. Прокси используется для навешивания пре и пост событий на процесс готовки. Например, если бургер не соответствует стандарту, пост событие утилизирует его.
5. Стратегия будет отвечать за то, что нужно приготовить.
6. Все сущности должны по максимуму генерироваться через DI.

---

Пример работы:

```php
$subject = new CookingStatusSubject();
$subject->attach(new NotificationCookingObserver(new ConsoleNotifier()));

$factoryProvider = new FoodFactoryProvider(
    new ClassicFoodFactory(),
    new VegetarianFoodFactory(),
);

$strategyResolver = new CookStrategyResolver([
    new BurgerCookStrategy(),
    new SandwichCookStrategy(),
    new HotdogCookStrategy(),
]);

$assembler = new ProductAssembler();

$cookingService = new CookingService($subject);
$proxyCooking = new CookingServiceProxy($cookingService, $subject);

$orderService = new OrderService(
    factoryProvider: $factoryProvider,
    strategyResolver: $strategyResolver,
    assembler: $assembler,
    cookingService: $proxyCooking,
);

// Бургер по рецепту
$recipeOrder = new Order(
    type: ProductType::BURGER,
    vegetarian: false,
    optional: []
);

// Кастомный бургер
$optionalOrder = new Order(
    type: ProductType::BURGER,
    vegetarian: true,
    optional: [BunDecorator::BUN, LettuceDecorator::LETTUCE, OnionDecorator::ONION]
);

try {
    $product = $orderService->placeOrder($recipeOrder);

    echo "RESULT: {$product->getName()}" . PHP_EOL;
    echo "STATUS: {$product->getCookingStatus()}" . PHP_EOL;
    print_r($product->getIngredients());
} catch (\Throwable $e) {
    echo "ERROR: {$e->getMessage()}" . PHP_EOL;
}
```