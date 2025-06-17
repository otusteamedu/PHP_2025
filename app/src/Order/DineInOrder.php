<?php
namespace App\Order;

class DineInOrder extends AbstractOrder {
    protected function handlePackaging(): void {
        echo "[В зале] Подача на поднос." . PHP_EOL;
    }
}
