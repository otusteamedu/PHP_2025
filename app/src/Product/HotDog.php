<?php
namespace App\Product;

class HotDog implements Food {
    public function getName(): string {
        return "Классический хот-дог";
    }

    public function prepare(): string {
        return "Основа: булочка для хотдога, сосиска, кетчуп";
    }

    public function getType(): string {
        return 'hotdog';
    }
}