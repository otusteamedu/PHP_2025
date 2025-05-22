<?php
namespace App\Products;

class HotDog implements ProductInterface
{
    public function getName(): string
    {
        return "Хот-дог";
    }
    
    public function getPrice(): float
    {
        return 3.99;
    }
    
    public function getDescription(): string
    {
        return "Классический хот-дог с сосиской";
    }
}
