<?php

use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Product\BurgerProduct;

class BurgerProductTest extends TestCase
{
    public function testMakeProductWithRecipe()
    {
        $recipe = "Секретный соус";
        $burger = new BurgerProduct($recipe);
        $title = "Чиз";

        $expectedOutput = "Чиз Бургер. Секретный соус";
        $this->assertEquals($expectedOutput, $burger->makeProduct($title), "Метод makeProduct() должен вернуть название с рецептом");
    }

    public function testMakeProductWithoutRecipe()
    {
        $burger = new BurgerProduct();
        $title = "Вегги";

        $expectedOutput = "Вегги Бургер. Стандартный рецепт";
        $this->assertEquals($expectedOutput, $burger->makeProduct($title), "Метод makeProduct() должен вернуть название с стандартным рецептом");
    }
}