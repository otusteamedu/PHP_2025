<?php

namespace Shop\Order;

interface OrderComponent
{
    public function getPrice(): float;
    public function getDescription(): string;
    public function getName(): string;
}