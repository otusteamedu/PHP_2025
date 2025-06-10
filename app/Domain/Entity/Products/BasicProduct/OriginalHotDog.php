<?php
declare(strict_types=1);

namespace App\Domain\Entity\Products\BasicProduct;

use App\Domain\Entity\Products\Product;

class OriginalHotDog extends Product
{

    private const NAME_PRODUCT = 'Оригинальный хот-дог';
    private const PRICE = 180.00;

    public function __construct()
    {
        $this->setName(self::NAME_PRODUCT);
        $this->setPrice(self::PRICE);
    }
}
