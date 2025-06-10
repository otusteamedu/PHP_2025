<?php
declare(strict_types=1);

namespace App\Domain\Entity\Products\BasicProduct;

use App\Domain\Entity\Products\Product;

class SpicySandwich extends Product
{

    private const NAME_PRODUCT = 'Острый сэндвич';
    private const PRICE = 155.00;

    public function __construct()
    {
        $this->setName(self::NAME_PRODUCT);
        $this->setPrice(self::PRICE);
    }
}