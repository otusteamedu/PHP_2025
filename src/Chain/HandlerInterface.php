<?php declare(strict_types=1);

namespace App\Chain;

use App\Core\FoodProductInterface;

interface HandlerInterface
{
    public function setNext(HandlerInterface $handler): HandlerInterface;
    public function handle(FoodProductInterface $product): void;
}
