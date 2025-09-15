<?php declare(strict_types=1);

use Fastfood\DI\Container;
use Fastfood\Services\MenuService;

require dirname(__DIR__) . '/vendor/autoload.php';
require_once 'config/di.php';

/**
 * Консольное приложение для заказа блюд быстрого приготовления
 */
class FastFoodConsoleApp
{
    private Container $container;
    private MenuService $menuService;
    private array $productsInfo;
    private array $ingredientsInfo;

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->menuService = $container->get('menu.service');
        $this->productsInfo = $this->menuService->getProductsInfo();
        $this->ingredientsInfo = $this->menuService->getIngredientsInfo();
    }

    public function run(): void
    {
        $orderData = [];
        $continue = true;

        while ($continue) {
            echo "=== FastFood Restaurant Console App ===\n\n";
            echo "Выберите товар для добавления в свой заказ:\n";

            // Динамическое формирование меню продуктов
            $index = 1;
            $productChoices = [];
            foreach ($this->productsInfo as $type => $info) {
                echo "{$index}. {$info['name']} [{$info['base_price']} руб.]\n";
                $productChoices[$index] = $type;
                $index++;
            }
            echo "{$index}. Завершить заказ\n";
            $index++;
            echo "{$index}. Выход\n";

            $choice = (int)$this->getUserInput("Введите ваш выбор (1-{$index}): ");

            if ($choice >= 1 && $choice <= $index - 2) {
                $productType = $productChoices[$choice];
                $ingredients = $this->selectIngredients($productType);

                $orderData[] = [
                    'type' => $productType,
                    'ingredients' => $ingredients
                ];
            } elseif ($choice === $index - 1) {
                if (empty($orderData)) {
                    echo "Ваш заказ пуст. Пожалуйста, добавьте продукты.\n\n";
                    $this->getUserInput("Нажмите Enter для продолжения...");
                    continue;
                }
                $this->processOrder($orderData);
                $orderData = [];

            } elseif ($choice === $index) {
                echo "Спасибо за использование нашего сервиса!\n";
                $continue = false;
            } else {
                echo "Неверный выбор. Пожалуйста, попробуйте снова.\n\n";
                $this->getUserInput("Нажмите Enter для продолжения...");
            }
        }
    }

    private function selectIngredients(string $productType): ?array
    {
        echo "\n--- Выберите ингредиенты для вашего {$this->productsInfo[$productType]['name']} ---\n";

        // Показываем базовые ингредиенты
        $basicIngredients = $this->productsInfo[$productType]['basic_ingredients'];
        echo "Базовые ингредиенты: ";
        $basicIngredientNames = array_map(function ($ingredient) {
            return $this->ingredientsInfo[$ingredient]['name'] ?? $ingredient;
        }, $basicIngredients);
        echo implode(', ', $basicIngredientNames) . "\n\n";

        $index = 1;
        $ingredientChoices = [];
        foreach ($this->ingredientsInfo as $key => $info) {
            // Отмечаем базовые ингредиенты
            $isChecked = in_array($key, $basicIngredients) ? ' [x]' : '';
            echo "{$index}. {$info['name']} (+{$info['price']} руб.){$isChecked}\n";
            $ingredientChoices[$index] = $key;
            $index++;
        }

        echo "{$index}. Завершить выбор ингредиентов\n";

        // Автоматически выбираем базовые ингредиенты
        $selectedIngredients = $basicIngredients;
        $selecting = true;

        while ($selecting) {
            $choice = (int)$this->getUserInput("Выберите ингредиент (1-{$index}): ");

            if ($choice >= 1 && $choice <= count($this->ingredientsInfo)) {
                $ingredientKey = $ingredientChoices[$choice];
                if (!in_array($ingredientKey, $selectedIngredients)) {
                    $selectedIngredients[] = $ingredientKey;
                    echo "Добавлен: {$this->ingredientsInfo[$ingredientKey]['name']}\n";
                } else {
                    // Убираем ингредиент, если он уже был выбран
                    $selectedIngredients = array_filter($selectedIngredients, function ($item) use ($ingredientKey) {
                        return $item !== $ingredientKey;
                    });
                    // Переиндексируем массив после фильтрации
                    $selectedIngredients = array_values($selectedIngredients);
                    echo "Убран: {$this->ingredientsInfo[$ingredientKey]['name']}\n";
                }
            } elseif ($choice === $index) {
                // Проверяем, что пользователь не убрал все базовые ингредиенты
                $missingBasics = array_diff($basicIngredients, $selectedIngredients);
                if (!empty($missingBasics) || empty($selectedIngredients)) {
                    if (!empty($missingBasics)) {
                        echo "Предупреждение: Вы убрали следующие базовые ингредиенты: ";
                        $missingNames = array_map(function ($ingredient) {
                            return $this->ingredientsInfo[$ingredient]['name'] ?? $ingredient;
                        }, $missingBasics);
                        echo implode(', ', $missingNames) . "\n";
                    } else {
                        echo "Предупреждение: Вы убрали все ингредиенты, включая базовые.\n";
                    }
                    
                    echo "Вы уверены? (y/N): ";
                    $confirm = strtolower(trim(fgets(STDIN)));
                    if ($confirm !== 'y' && $confirm !== 'yes') {
                        $selectedIngredients = array_unique(array_merge($selectedIngredients, $basicIngredients));
                        echo "Базовые ингредиенты восстановлены.\n";
                    }
                }
                $selecting = false;
            } else {
                echo "Неверный выбор. Пожалуйста, попробуйте снова.\n";
            }
        }

        if (empty($selectedIngredients)) {
            return [];
        }
        
        return array_values($selectedIngredients);
    }

    private function processOrder(array $orderData): void
    {
        try {

            $orderService = $this->container->get('order.service');
            
            print_r("\n=== Создание заказа ===\n");
            $orderService->createOrder($orderData);

            $this->getUserInput("Нажмите Enter для возврата в главное меню...");

        } catch (Exception $e) {
            echo "\nОшибка при обработке заказа: " . $e->getMessage() . "\n";
            $this->getUserInput("Нажмите Enter для продолжения...");
        }
    }

    private function getUserInput(string $prompt): string
    {
        echo $prompt;
        $handle = fopen("php://stdin", "r");
        $input = trim(fgets($handle));
        fclose($handle);
        return $input;
    }

}

try {
    global $container;
    $app = new FastFoodConsoleApp($container);
    $app->run();
} catch (Exception $e) {
    echo "Ошибка приложения: " . $e->getMessage() . "\n";
}