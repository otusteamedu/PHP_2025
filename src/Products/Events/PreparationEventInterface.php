<?php declare(strict_types=1);

namespace Fastfood\Products\Events;

use Fastfood\Products\Entity\ProductInterface;

interface PreparationEventInterface
{
    public function getProduct(): ProductInterface;
    public function getEventType(): string;
    public function getTimestamp(): int;
}