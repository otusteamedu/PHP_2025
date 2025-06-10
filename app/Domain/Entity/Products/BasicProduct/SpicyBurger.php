<?php
declare(strict_types=1);

namespace App\Domain\Entity\Products\BasicProduct;

use App\Domain\Entity\Products\Product;

class SpicyBurger extends Product
{

    private const NAME_PRODUCT = 'Острый бургер';
    private const PRICE = 260.00;

    public function __construct()
    {
        $this->setName(self::NAME_PRODUCT);
        $this->setPrice(self::PRICE);
    }
}