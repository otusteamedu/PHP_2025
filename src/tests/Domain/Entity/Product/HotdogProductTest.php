<?php

use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Product\HotdogProduct;

class HotdogProductTest extends TestCase
{
    public function testMakeProductWithRecipe()
    {
        $recipe = "Секретный соус";
        $burger = new HotdogProduct($recipe);
        $title = "Чиз";

        $expectedOutput = "Чиз Хотдог. Секретный соус";
        $this->assertEquals($expectedOutput, $burger->makeProduct($title), "Метод makeProduct() должен вернуть название с рецептом");
    }

    public function testMakeProductWithoutRecipe()
    {
        $burger = new HotdogProduct();
        $title = "Вегги";

        $expectedOutput = "Вегги Хотдог. Стандартный рецепт";
        $this->assertEquals($expectedOutput, $burger->makeProduct($title), "Метод makeProduct() должен вернуть название с стандартным рецептом");
    }
}