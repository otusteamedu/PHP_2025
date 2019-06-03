<?php
namespace app\tests;

use app\models\Products;

class ProductClassTest extends \PHPUnit\Framework\TestCase {
    public function testProduct() {

        $name = "Чай";
        $product = new Products("Чай","Цейлонский", 234);
        $this->assertEquals("Чай", $product->getNameProduct());
    }
}