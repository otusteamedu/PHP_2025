<?php

declare(strict_types=1);

namespace App\Models;

class Order
{
    private ?int $id;

    private string $client_name;

    private string $client_phone;

    private string $product_id;

    private string $status;

    private ?string $product_name;

    private ?float $product_price;

    private string $payment_link;

    private array $ingredients;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param ?int $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getClientName(): string
    {
        return $this->client_name;
    }

    /**
     * @param string $client_name
     */
    public function setClientName(string $client_name): void
    {
        $this->client_name = $client_name;
    }

    /**
     * @return string
     */
    public function getClientPhone(): string
    {
        return $this->client_phone;
    }

    /**
     * @param string $client_phone
     */
    public function setClientPhone(string $client_phone): void
    {
        $this->client_phone = $client_phone;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->product_id;
    }

    /**
     * @param string $product_id
     */
    public function setProductId(string $product_id): void
    {
        $this->product_id = $product_id;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @param string $payment_link
     */
    public function setPaymentLink(string $payment_link): void
    {
        $this->payment_link = $payment_link;
    }

    /**
     * @return array
     */
    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    /**
     * @param array $ingredients
     * @return void
     */
    public function setIngredients(array $ingredients): void
    {
        $this->ingredients = $ingredients;
    }

    /**
     * @return string
     */
    public function getProductName(): string
    {
        return $this->product_name;
    }

    /**
     * @param ?string $product_name
     */
    public function setProductName(?string $product_name): void
    {
        $this->product_name = $product_name;
    }

    /**
     * @return float
     */
    public function getProductPrice(): float
    {
        return $this->product_price;
    }

    /**
     * @param ?float $product_price
     */
    public function setProductPrice(?float $product_price): void
    {
        $this->product_price = $product_price;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'client_name' => $this->client_name,
            'client_phone' => $this->client_phone,
            'product_id' => $this->product_id ?? null,
            'status' => $this->status,
            'ingredients' => $this->ingredients,
            'product_name' => $this->product_name,
            'product_price' => $this->product_price,
            'payment_link' => $this->payment_link ?? null
        ];
    }
}
