<?php
namespace App\Cooking;

class BurgerCookingProcess extends AbstractCookingProcess {
    protected function checkIngredients(): void {
        echo "[Проверка ингредиентов] Для бургера: булочка, котлета, кетчуп" . PHP_EOL;
    }

    protected function qualityControl(): void {
        echo "[Проверка качества] Качество бургера: температура OK, визуальный контроль OK" . PHP_EOL;
    }
}
