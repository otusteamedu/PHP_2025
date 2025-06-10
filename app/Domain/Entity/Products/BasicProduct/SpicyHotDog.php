<?php
declare(strict_types=1);

namespace App\Domain\Entity\Products\BasicProduct;

use App\Domain\Entity\Products\Product;

class SpicyHotDog extends Product
{

    private const NAME_PRODUCT = 'Острый хот-дог';
    private const PRICE = 188.00;

    public function __construct()
    {
        $this->setName(self::NAME_PRODUCT);
        $this->setPrice(self::PRICE);
    }
}