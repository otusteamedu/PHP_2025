<?php

declare(strict_types=1);

namespace App\Application\DTO;

class CreateOrderRequest
{
    public function __construct(
        public readonly array $items = []
    ) {}

    public static function fromArray(array $data): self
    {
        if (isset($data['product_type'])) {
            return new self(
                items: [
                    [
                        'product_type' => $data['product_type'],
                        'additions' => $data['additions'] ?? []
                    ]
                ]
            );
        }

        return new self(
            items: $data['items'] ?? []
        );
    }
}
