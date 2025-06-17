<?php
namespace App\Pizza;

class Pizza {
    public function bake(): string {
        return "Тесто, томатный соус, сыр - выпекать при 220°C";
    }

    public function name(): string {
        return "Неаполитанская пицца";
    }
}