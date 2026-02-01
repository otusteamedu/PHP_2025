<?php

declare(strict_types=1);

namespace Otus\Food\Domain\Order;

use Random\RandomException;

readonly class Order
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @param Buyer $buyer
     */
    public function __construct(private Buyer $buyer)
    {
        try {
            $this->id = bin2hex(random_bytes(16));
        } catch (RandomException) {
            // todo
        }
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Buyer
     */
    public function getBuyer(): Buyer
    {
        return $this->buyer;
    }
}
