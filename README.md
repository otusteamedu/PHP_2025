# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

Реализован интернет-ресторан фаст-фуда с использованием следующих паттернов проектирования:

1. Стратегия - генерация базового продукта
   ProductStrategyInterface - интерфейс стратегии
   BurgerStrategy, SandwichStrategy, HotDogStrategy - конкретные стратегии
   ProductFactory - фабрика, использующая стратегии
2. Декоратор - добавки к продуктам
   ProductDecorator - базовый декоратор
   LettuceDecorator, OnionDecorator, PepperDecorator, CheeseDecorator, TomatoDecorator
3. Наблюдатель - уведомления о статусе заказа
   OrderObserverInterface, OrderSubjectInterface
   OrderSubject - субъект наблюдения
   PushNotificationObserver, SmsNotificationObserver - заглушки уведомлений
4. Шаблонный метод - процесс готовки с пре/пост событиями
   AbstractCookingProcess - базовый класс с beforeCooking() → doCooking() → afterCooking()
   BurgerCookingProcess, SandwichCookingProcess, HotDogCookingProcess
5. Строитель - формирование заказа
   OrderBuilderInterface, OrderBuilder

DI Container
Все сущности генерируются через ContainerBuilder и ServiceProvider

API:
GET / - информация об API
GET /menu - получить меню ресторана
POST /order - создать заказ (body: {"product_type": "burger", "additions": ["lettuce", "cheese"]})
