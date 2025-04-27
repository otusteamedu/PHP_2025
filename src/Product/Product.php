<?php

namespace Hafiz\Php2025\Product;

interface Product {
    public function getDescription(): string;
    public function getCost(): float;
}
