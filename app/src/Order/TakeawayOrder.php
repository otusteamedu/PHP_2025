<?php
namespace App\Order;

class TakeawayOrder extends AbstractOrder {
    protected function handlePackaging(): void {
        echo "[C собой] Заворачиваем в пакет." . PHP_EOL;
    }
}
