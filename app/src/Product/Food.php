<?php
namespace App\Product;

interface Food {
    public function getName(): string;
    public function prepare(): string;
    public function getType(): string;
}