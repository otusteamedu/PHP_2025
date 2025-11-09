<?php

declare(strict_types=1);

namespace App\Builders;

use App\Products\ProductInterface;

interface ProductBuilderInterface
{
    public function addLettuce(): self;
    public function addTomato(): self;
    public function addCheese(): self;
    public function addOnion(): self;
    public function addBacon(): self;
    public function addPickles(): self;
    public function addMustard(): self;
    public function addKetchup(): self;
    public function build(): ProductInterface;
}
