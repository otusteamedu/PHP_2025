<?php
namespace App\Products;

class Sandwich implements ProductInterface
{
    public function getName(): string
    {
        return "Сэндвич";
    }
    
    public function getPrice(): float
    {
        return 4.49;
    }
    
    public function getDescription(): string
    {
        return "Свежий сэндвич с курицей";
    }
}
