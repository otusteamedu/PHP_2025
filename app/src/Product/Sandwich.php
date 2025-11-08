<?php
namespace App\Product;

class Sandwich implements Food {
    public function getName(): string {
        return "Сэндвич Изысканный";
    }

    public function prepare(): string {
        return "Основа: хлеб, ветчина, чесночный соус";
    }

    public function getType(): string {
        return 'sandwich';
    }
}