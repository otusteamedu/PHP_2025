<?php

namespace App\Adapter;

class Pizza {
    public function bake(): string {
        return 'Pizza with tomato, cheese';
    }

    public function price(): float {
        return 8.5;
    }
}