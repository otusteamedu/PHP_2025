<?php

namespace App\Iterator;

class OrderTracker {
    private OrderStatus $status = OrderStatus::Received;

    public function next(): void {
        $this->status = match ($this->status) {
            OrderStatus::Received => OrderStatus::Preparing,
            OrderStatus::Preparing => OrderStatus::Ready,
            OrderStatus::Ready => OrderStatus::Delivered,
            OrderStatus::Delivered => OrderStatus::Delivered,
        };
    }

    public function getStatus(): OrderStatus {
        return $this->status;
    }
}