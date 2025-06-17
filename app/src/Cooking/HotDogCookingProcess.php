<?php
namespace App\Cooking;

class HotDogCookingProcess extends AbstractCookingProcess {
    protected function checkIngredients(): void {
        echo "[Проверка ингредиентов] Для хотдога: булочка для хотдога, сосиска, кетчуп" . PHP_EOL;
    }

    protected function qualityControl(): void {
        echo "[Проверка качества] Качество хотдога: температура OK, визуальный контроль OK" . PHP_EOL;
    }
}