<?php
namespace App\Cooking;

class PizzaCookingProcess extends AbstractCookingProcess {
    protected function checkIngredients(): void {
        echo "[Проверка ингредиентов] Для пиццы: тесто, томатный соус, сыр" . PHP_EOL;
    }

    protected function qualityControl(): void {
        echo "[Проверка качества] Качество пиццы: корочка запеченная, сыр расплавленный" . PHP_EOL;
    }
}
