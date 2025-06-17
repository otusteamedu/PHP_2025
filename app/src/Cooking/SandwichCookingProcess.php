<?php
namespace App\Cooking;

class SandwichCookingProcess extends AbstractCookingProcess {
    protected function checkIngredients(): void {
        echo "[Проверка ингредиентов] Для сендвича: хлеб, ветчина, чесночный соус" . PHP_EOL;
    }

    protected function qualityControl(): void {
        echo "[Проверка качества] Качество сендвича: температура OK, визуальный контроль OK" . PHP_EOL;
    }
}