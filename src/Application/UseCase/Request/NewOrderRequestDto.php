<?php

namespace Blarkinov\Hw1500\Application\UseCase\Request;


class NewOrderRequestDto
{

    public function __construct(
        private array $orderFood,
    ) {}

    public function getOrderFood(): array
    {
        return $this->orderFood;
    }
}
