<?php declare(strict_types=1);

namespace Fastfood\Factories;

use Exception;
use Fastfood\DI\Container;
use Fastfood\Products\Decorators;
use Fastfood\Products\Decorators\IngredientDecoratorInterface;
use Fastfood\Products\Entity\ProductInterface;
use Fastfood\Products\Events\PostPreparationEvent;
use Fastfood\Products\Events\PrePreparationEvent;

class ProductFactory
{
    private array $decoratorMap = [];
    private array $eventListeners = [];

    public function __construct(private Container $container)
    {
        $this->initializeDecoratorMap();
    }

    /**
     * Метод динамического создания карты декораторов
     *
     * @throws Exception
     */
    private function initializeDecoratorMap(): void
    {
        // Создаем базовый продукт
        $builder = $this->container->get('product.builder.burger');
        $builder->setBase();
        $product = $builder->getProduct();

        // Сканируем директорию с декораторами
        $decoratorsDir = __DIR__ . '/../Products/Decorators';
        $files = scandir($decoratorsDir);

        foreach ($files as $file) {
            if (str_contains($file, 'Decorator.php')) {
                $className = 'Fastfood\\Products\\Decorators\\' . pathinfo($file, PATHINFO_FILENAME);
                if (class_exists($className)) {
                    $reflection = new \ReflectionClass($className);
                    if ($reflection->implementsInterface(IngredientDecoratorInterface::class)) {
                        $tempInstance = new $className($product);
                        $this->decoratorMap[$tempInstance->getKey()] = $className;
                    }
                }
            }
        }
    }

    public function addEventListener($listener): void
    {
        $this->eventListeners[] = $listener;
    }

    /**
     * @throws Exception
     */
    public function createBurger(?array $ingredients = null): ProductInterface
    {
        $builder = $this->container->get('product.builder.burger');
        $basicIngredients = $builder->getBasicIngredients();
        $builder->setBase();

        $product = $builder->getProduct();

        // Handle ingredients properly
        if ($ingredients === null) {
            // If no ingredients specified, use basic ingredients
            $ingredients = $basicIngredients;
        } elseif (is_array($ingredients) && count($ingredients) === 0) {
            // If ingredients is explicitly an empty array, use no additional ingredients
            $ingredients = [];
        } else {
            // Merge with basic ingredients and remove duplicates
            $ingredients = array_unique(array_merge($basicIngredients, $ingredients));
        }

        foreach ($ingredients as $ingredient) {
            $decoratorClass = $this->decoratorMap[$ingredient] ?? null;
            if ($decoratorClass && class_exists($decoratorClass)) {
                $product = new $decoratorClass($product);
            }
        }

        return $this->applyQualityControl($product, $ingredients);
    }

    /**
     * @throws Exception
     */
    public function createSandwich(?array $ingredients = null): ProductInterface
    {
        $builder = $this->container->get('product.builder.sandwich');
        $basicIngredients = $builder->getBasicIngredients();
        $builder->setBase();

        $product = $builder->getProduct();

        // Handle ingredients properly
        if ($ingredients === null) {
            // If no ingredients specified, use basic ingredients
            $ingredients = $basicIngredients;
        } elseif (is_array($ingredients) && count($ingredients) === 0) {
            // If ingredients is explicitly an empty array, use no additional ingredients
            $ingredients = [];
        } else {
            // Merge with basic ingredients and remove duplicates
            $ingredients = array_unique(array_merge($basicIngredients, $ingredients));
        }

        foreach ($ingredients as $ingredient) {
            $decoratorClass = $this->decoratorMap[$ingredient] ?? null;
            if ($decoratorClass && class_exists($decoratorClass)) {
                $product = new $decoratorClass($product);
            }
        }

        return $this->applyQualityControl($product, $ingredients);
    }

    /**
     * @throws Exception
     */
    public function createHotdog(?array $ingredients = null): ProductInterface
    {
        $builder = $this->container->get('product.builder.hotdog');
        $basicIngredients = $builder->getBasicIngredients();
        $builder->setBase();

        $product = $builder->getProduct();

        // Handle ingredients properly
        if ($ingredients === null) {
            // If no ingredients specified, use basic ingredients
            $ingredients = $basicIngredients;
        } elseif (is_array($ingredients) && count($ingredients) === 0) {
            $ingredients = [];
        } else {
            $ingredients = array_unique(array_merge($basicIngredients, $ingredients));
        }

        foreach ($ingredients as $ingredient) {
            $decoratorClass = $this->decoratorMap[$ingredient] ?? null;
            if ($decoratorClass && class_exists($decoratorClass)) {
                $product = new $decoratorClass($product);
            }
        }

        return $this->applyQualityControl($product, $ingredients);
    }

    /**
     * Calculate the cost of a product based on its base cost and ingredients
     *
     * @param string $productType The type of product (burger, sandwich, hotdog)
     * @param array|null $ingredients List of ingredients
     * @return float The calculated cost
     * @throws Exception
     */
    public function calculateProductCost(string $productType, ?array $ingredients = null): float
    {
        // Get the base product
        $builder = match($productType) {
            'burger' => $this->container->get('product.builder.burger'),
            'sandwich' => $this->container->get('product.builder.sandwich'),
            'hotdog' => $this->container->get('product.builder.hotdog'),
            default => throw new Exception("Unknown product type: " . $productType)
        };

        $basicIngredients = $builder->getBasicIngredients();
        $builder->setBase();
        $baseProduct = $builder->getProduct();
        
        // Start with base product cost
        $totalCost = $baseProduct->getCost();

        // Handle ingredients properly
        if ($ingredients === null) {
            // If no ingredients specified, use basic ingredients
            $ingredients = $basicIngredients;
        } elseif (is_array($ingredients) && count($ingredients) === 0) {
            // If ingredients is explicitly an empty array, use no additional ingredients
            $ingredients = [];
        } else {
            // Merge with basic ingredients and remove duplicates
            $ingredients = array_unique(array_merge($basicIngredients, $ingredients));
        }

        // Add cost of each ingredient
        foreach ($ingredients as $ingredient) {
            $decoratorClass = $this->decoratorMap[$ingredient] ?? null;
            if ($decoratorClass && class_exists($decoratorClass)) {
                // Create a temporary decorator to get its price
                $tempDecorator = new $decoratorClass($baseProduct);
                $totalCost += $tempDecorator->getPrice();
            }
        }

        return $totalCost;
    }

    /**
     * @throws Exception
     */
    private function applyQualityControl(ProductInterface $product, array $ingredients): ProductInterface
    {
        // Вызываем пре-событие
        $preEvent = new PrePreparationEvent($product);
        foreach ($this->eventListeners as $listener) {
            $listener->onPrePreparation($preEvent);
        }

        // Проверка перед приготовлением
        $preCheckResult = $this->preCookCheck($product, $ingredients);

        // Проверка после приготовления
        $postCheckResult = $this->postCookCheck($product, $ingredients);

        // Вызываем пост-событие
        $postEvent = new PostPreparationEvent(
            $product,
            $postCheckResult['passed'],
            $postCheckResult['message']
        );

        foreach ($this->eventListeners as $listener) {
            $listener->onPostPreparation($postEvent);
        }

        // Если продукт не прошел контроль качества, попробуем переприготовить
        if (!$preCheckResult['passed'] || !$postCheckResult['passed']) {
            try {
                $recookedProduct = $this->recookProduct($product, $ingredients);
                return $recookedProduct;
            } catch (Exception $e) {
                // Если повторное приготовление не помогло, выбрасываем исключение
                $errorMessage = !$preCheckResult['passed'] ? $preCheckResult['message'] : $postCheckResult['message'];
                throw new Exception('Продукт не прошел проверку качества даже после повторного приготовления: ' . $errorMessage);
            }
        }

        // Добавление информации о проверке качества к продукту (для отображения во внешнем интерфейсе)
        $reflection = new \ReflectionClass($product);
        if ($reflection->hasProperty('qualityInfo')) {
            $property = $reflection->getProperty('qualityInfo');
            $property->setAccessible(true);
            $qualityInfo = $property->getValue($product) ?? [];
            $qualityInfo['pre_cook_check'] = $preCheckResult;
            $qualityInfo['post_cook_check'] = $postCheckResult;
            $property->setValue($product, $qualityInfo);
        }

        return $product;
    }

    /**
     * Повторное приготовление продукта в случае неудачного контроля качества
     * Продолжаем повторное приготовление до тех пор, пока все проверки не пройдут успешно
     * или пока не будет достигнуто максимальное количество попыток
     * @throws Exception
     */
    private function recookProduct(ProductInterface $originalProduct, array $ingredients): ProductInterface
    {
        $maxAttempts = 5; // Максимальное количество попыток повторного приготовления
        $attempt = 1;
        $lastErrorMessage = '';

        print_r("\nПОВТОРНОЕ ПРИГОТОВЛЕНИЕ: Продукт '" . $originalProduct->getDescription() . "' не прошел контроль качества. Начинаем повторное приготовление.");

        do {
            print_r("\nПОВТОРНОЕ ПРИГОТОВЛЕНИЕ: Попытка #$attempt из $maxAttempts");

            // Создаем новый продукт с теми же ингредиентами
            // Определяем тип продукта по описанию оригинального продукта
            $description = $originalProduct->getDescription();
            
            // Пытаемся создать новый продукт с теми же ингредиентами
            // В реальной реализации можно добавить небольшие изменения в процесс приготовления
            $newProduct = clone $originalProduct;
            
            // Повторно применяем декораторы к новому продукту
            foreach ($ingredients as $ingredient) {
                $decoratorClass = $this->decoratorMap[$ingredient] ?? null;
                if ($decoratorClass && class_exists($decoratorClass)) {
                    $newProduct = new $decoratorClass($newProduct);
                }
            }

            // Проводим повторную проверку качества
            $preCheckResult = $this->preCookCheck($newProduct, $ingredients);
            $postCheckResult = $this->postCookCheck($newProduct, $ingredients);

            // Если все проверки пройдены, возвращаем продукт
            if ($preCheckResult['passed'] && $postCheckResult['passed']) {
                // Добавляем информацию о повторном приготовлении
                $reflection = new \ReflectionClass($newProduct);
                if ($reflection->hasProperty('qualityInfo')) {
                    $property = $reflection->getProperty('qualityInfo');
                    $property->setAccessible(true);
                    $qualityInfo = $property->getValue($newProduct) ?? [];
                    $qualityInfo['pre_cook_check'] = $preCheckResult;
                    $qualityInfo['post_cook_check'] = $postCheckResult;
                    $qualityInfo['recooked'] = true;
                    $qualityInfo['recook_attempts'] = $attempt;
                    $property->setValue($newProduct, $qualityInfo);
                }

                echo("\nПОВТОРНОЕ ПРИГОТОВЛЕНИЕ: Продукт успешно прошел контроль качества после повторного приготовления. Попыток: $attempt");
                return $newProduct;
            }

            // Сохраняем последнюю ошибку
            $lastErrorMessage = !$preCheckResult['passed'] ? $preCheckResult['message'] : $postCheckResult['message'];
            echo("\nПОВТОРНОЕ ПРИГОТОВЛЕНИЕ: Попытка #$attempt неудачна. Причина: $lastErrorMessage");

            $attempt++;
        } while ($attempt <= $maxAttempts);

        // Если достигнуто максимальное количество попыток, выбрасываем исключение
        throw new Exception('\nПовторная проверка качества не пройдена после ' . $maxAttempts . ' попыток: ' . $lastErrorMessage);
    }

    private function preCookCheck(ProductInterface $product, array $ingredients): array
    {
        $checks = [];
        $passed = true;
        $messages = [];

        // Пример: Проверка на достаточное кол-во ингридиентов
        $ingredientCount = count($ingredients);

        if ($ingredientCount < 1) {
            $checks[] = [
                'name' => 'Minimum ingredients',
                'passed' => false,
                'message' => 'В продукте слишком мало ингредиентов'
            ];
            $passed = false;
            $messages[] = 'В продукте слишком мало ингредиентов';
        } else {
            $checks[] = [
                'name' => 'Minimum ingredients',
                'passed' => true,
                'message' => 'Продукт содержит достаточное количество ингредиентов'
            ];
        }

        // Пример: Проверка типа оплаты заказа,
        // если предоплата -> проверить оплату ("эмуляция" запроса к сервису и получение ответов)
        if ($this->getPaymentType(rand(2,5)) === "pre") {
            if ($this->getCheckPayment(rand(2,5))) {
                $checks[] = [
                    'name' => 'Payment',
                    'passed' => true,
                    'message' => 'Продукт оплачен'
                ];
            } else {
                $checks[] = [
                    'name' => 'Payment',
                    'passed' => false,
                    'message' => 'Продукт не оплачен'
                ];
                $passed = false;
                $messages[] = 'Продукт не оплачен';
            }
        } else {
            $checks[] = [
                'name' => 'Payment',
                'passed' => true,
                'message' => 'Продукт будет оплачен при получении'
            ];
        }

        return [
            'passed' => $passed,
            'message' => $passed ? 'Все предварительные проверки приготовления пройдены' : implode(', ', $messages),
            'checks' => $checks
        ];
    }

    private function postCookCheck(ProductInterface $product, array $ingredients): array
    {
        $checks = [];
        $passed = true;
        $messages = [];

        // Пример: Проверка на время выпекания булочки/хлеба
        $baking_time = rand(55, 65);
        $good_time = [59, 60, 61];

        if (in_array($baking_time, $good_time)) {
            $checks[] = [
                'name' => 'Baking preparation time',
                'passed' => true,
                'message' => 'Булочка/хлеб выпекалась согласно требованиям'
            ];
        } else {
            $checks[] = [
                'name' => 'Baking preparation time',
                'passed' => false,
                'message' => 'Булочка/хлеб выпекалась с нарушением требования'
            ];
            $passed = false;
            $messages[] = 'Булочка/хлеб выпекалась с нарушением требования';
        }

        return [
            'passed' => $passed,
            'message' => $passed ? 'Все проверки после приготовления пройдены' : implode(', ', $messages),
            'checks' => $checks
        ];
    }

    private function getPaymentType(int $productId): string
    {
        if ($productId > 3)
            return "pre";
        else
            return "post";
    }

    private function getCheckPayment(int $productId): bool
    {
        if ($productId > 4) return false;

        return true;
    }
}