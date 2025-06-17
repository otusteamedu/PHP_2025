<?php
namespace App\Product;

class Burger implements Food {
    public function getName(): string {
        return "Фирменный Бургер";
    }

    public function prepare(): string {
        return "Основа: булочка, котлета, кетчуп";
    }

    public function getType(): string {
        return 'burger';
    }
}