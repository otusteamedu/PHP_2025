<?php
// config/services.php
use App\Infrastructure\Strategies\BurgerPrototypeStrategy;
use App\Infrastructure\Strategies\SandwichPrototypeStrategy;
use App\Infrastructure\Strategies\HotDogPrototypeStrategy;
use App\Infrastructure\Strategies\ProductPrototypeFactory;
use App\Infrastructure\Decorators\RecipeDecorator;
use App\Infrastructure\Observers\ProductStatusLogger;
use App\Infrastructure\Observers\EmailNotifier;
use App\Domain\Observable\ProductSubject;
use App\Infrastructure\Templates\BurgerCookingTemplate;
use App\Infrastructure\Templates\SandwichCookingTemplate;
use App\Infrastructure\Templates\HotDogCookingTemplate;
use App\Infrastructure\Builders\OrderBuilder;
use App\Application\Services\KitchenService;
use App\Application\Services\OrderService;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Создаем логгер
$logger = new Logger('fastfood');
$logger->pushHandler(new StreamHandler(__DIR__ . '/../var/log/app.log', Logger::INFO));

return [
    // Логгер
    LoggerInterface::class => DI\value($logger),
    
    // Стратегии
    BurgerPrototypeStrategy::class => DI\create(),
    SandwichPrototypeStrategy::class => DI\create(),
    HotDogPrototypeStrategy::class => DI\create(),
    
    ProductPrototypeFactory::class => DI\autowire()
        ->constructorParameter('burgerStrategy', DI\get(BurgerPrototypeStrategy::class))
        ->constructorParameter('sandwichStrategy', DI\get(SandwichPrototypeStrategy::class))
        ->constructorParameter('hotDogStrategy', DI\get(HotDogPrototypeStrategy::class)),
    
    // Декораторы
    RecipeDecorator::class => DI\create(),
    
    // Наблюдатели
    ProductStatusLogger::class => DI\autowire()
        ->constructorParameter('logger', DI\get(LoggerInterface::class)),
    
    EmailNotifier::class => DI\create(),
    
    // Subject с наблюдателями
    ProductSubject::class => DI\autowire()
        ->method('attach', DI\get(ProductStatusLogger::class))
        ->method('attach', DI\get(EmailNotifier::class)),
    
    // Шаблоны приготовления
    BurgerCookingTemplate::class => DI\create(),
    SandwichCookingTemplate::class => DI\create(),
    HotDogCookingTemplate::class => DI\create(),
    
    // Строитель
    OrderBuilder::class => DI\autowire()
        ->constructorParameter('prototypeFactory', DI\get(ProductPrototypeFactory::class))
        ->constructorParameter('decorator', DI\get(RecipeDecorator::class)),
    
    // Сервисы
    KitchenService::class => DI\autowire(),
    OrderService::class => DI\autowire(),
];