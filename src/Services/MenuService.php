<?php declare(strict_types=1);

namespace Fastfood\Services;

use Exception;
use Fastfood\DI\Container;
use Fastfood\Products\Builders\ProductBuilderInterface;
use Fastfood\Products\Decorators\ProductDecorator;
use ReflectionClass;
use ReflectionException;

class MenuService
{
    private Container $container;
    private array $productTypes = [
        'burger' => [
            'name' => 'Бургер',
            'builder' => 'product.builder.burger'
        ],
        'sandwich' => [
            'name' => 'Сэндвич',
            'builder' => 'product.builder.sandwich'
        ],
        'hotdog' => [
            'name' => 'Хот-дог',
            'builder' => 'product.builder.hotdog'
        ]
    ];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Получить информацию обо всех продуктах
     *
     * @return array
     * @throws Exception
     */
    public function getProductsInfo(): array
    {
        $productsInfo = [];

        foreach ($this->productTypes as $type => $config) {
            $builder = $this->container->get($config['builder']);
            if ($builder instanceof ProductBuilderInterface) {
                $builder->setBase();
                $product = $builder->getProduct();

                $productsInfo[$type] = [
                    'name' => $product->getDescription(),
                    'base_price' => $product->getCost(),
                    'basic_ingredients' => $builder->getBasicIngredients()
                ];
            }
        }

        return $productsInfo;
    }

    /**
     * Получить информацию обо всех доступных ингредиентах
     *
     * @return array
     * @throws ReflectionException
     */
    public function getIngredientsInfo(): array
    {
        $decorators = [
            'cheese' => \Fastfood\Products\Decorators\CheeseDecorator::class,
            'salad' => \Fastfood\Products\Decorators\SaladDecorator::class,
            'onion' => \Fastfood\Products\Decorators\OnionDecorator::class,
            'tomato' => \Fastfood\Products\Decorators\TomatoDecorator::class,
            'bacon' => \Fastfood\Products\Decorators\BaconDecorator::class,
            'pepper' => \Fastfood\Products\Decorators\PepperDecorator::class,
            'mayo' => \Fastfood\Products\Decorators\MayoDecorator::class,
            'ketchup' => \Fastfood\Products\Decorators\KetchupDecorator::class
        ];

        $ingredientsInfo = [];

        foreach ($decorators as $key => $decoratorClass) {
            if (class_exists($decoratorClass)) {
                $reflection = new ReflectionClass($decoratorClass);
                $decorator = $reflection->newInstanceWithoutConstructor();

                if ($decorator instanceof ProductDecorator) {
                    // Создаем временный экземпляр для получения информации
                    $tempProduct = new \Fastfood\Products\Entity\Burger();
                    $tempProduct->setBase('temp', 0);
                    $decoratorInstance = new $decoratorClass($tempProduct);

                    $ingredientsInfo[$key] = [
                        'name' => $decoratorInstance->getName(),
                        'price' => $decoratorInstance->getPrice()
                    ];
                }
            }
        }

        return $ingredientsInfo;
    }
    
    /**
     * Получить информацию о конкретном продукте
     *
     * @param string $type
     * @return array|null
     * @throws Exception
     */
    public function getProductInfo(string $type): ?array
    {
        if (!isset($this->productTypes[$type])) {
            return null;
        }
        
        $config = $this->productTypes[$type];
        $builder = $this->container->get($config['builder']);
        
        if ($builder instanceof ProductBuilderInterface) {
            $builder->setBase();
            $product = $builder->getProduct();

            return [
                'name' => $product->getDescription(),
                'base_price' => $product->getCost(),
                'basic_ingredients' => $builder->getBasicIngredients()
            ];
        }
        
        return null;
    }
}