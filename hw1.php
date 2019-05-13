<?php
/*
5.
Дан код:
class A {
    public function foo() {
        static $x = 0;
        echo ++$x;
    }
}
$a1 = new A();
$a2 = new A();
$a1->foo();
$a2->foo();
$a1->foo();
$a2->foo();
Что он выведет на каждом шаге? Почему?

Ответ: Код выведет: 1234. Потому, что статические переменные сохраняют своё значение.

6. Объясните результаты в этом случае.
class A {
    public function foo() {
        static $x = 0;
        echo ++$x;
    }
}
class B extends A {
}
$a1 = new A();
$b1 = new B();
$a1->foo();
$b1->foo();
$a1->foo();
$b1->foo();

Ответ: Код выведет: 1122. Потому, что при наследовании, если функция содержит статическую переменную, то
в наследнике создаётся её дубликат со своими значениями статических переменных.
*/

// Класс корзины
class Cart{

    private $content = [/*id=>[Product, quantity]*/]; // ассоциативный массив где ключ это некий id, а значение это массив с товаром и количеством

    /**
     * Cart constructor.
     * @param array $products
     */
    public function __construct(array $cart)
    {

    }
    public function add(Product $product, $quantity){

        // добавляет позицию в корзину
    }
    public function remove(Product $product){

        // удаляяет позицию из корзины
    }
    private function findIndexOf(Product $product){

        // находит индекс определённого товара в корзине
    }
}
// Продвинутая корзина, использует промокоды, считает общую стоимость одной позиции и всей корзины в целом;
class AdvanceCart extends Cart{

    private $promoCode;

    public function __construct(array $cart, $promoCode)
    {
        parent::__construct($cart);
        $this->promoCode = $promoCode;
    }
    public function  getSum(Product $product){

        // возвращает общую стоимость одного товара с учётом его количества;
    }
    public function getTotal(){
        // возвращает общую стоимость товаров в корзине;
    }
}
// Класс товара. Содержит только базовые свойства;

class Product{

    private $name; // Название товара
    private $description; // Краткое описание
    private $price; // Цена

    /**
     * Product constructor.
     * @param $name
     * @param $description
     * @param $price
     */
    public function __construct($name, $price, $description='')
    {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
    }
    public function __toString()
    {
        $str = "Название: " . $this.$this->name . "</br>" . "Цена: " . $this->price;
        return $str;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

}
// Класс сертификата, наследника товара. Нужен для сертификатов
class Certificate extends Product{

    private $certNumber;
    private $issuer;

}
//Овощи и фрукты
class Vergitables extends Product{

    private $content; // Состав
    private $BJU; // БЖУ - белки, жиры, углеводы
    private $nutrients; // микроэлементы

}
// Железо
class Hardware extends Product{

    private $model; // модель
    private $serialNumber; // серийный номер
    private $vendor; // производитель

}
$cart = new Cart([]);

var_dump($cart);