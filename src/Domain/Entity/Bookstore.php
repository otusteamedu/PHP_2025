<?php

namespace BookstoreApp\Domain\Entity;

class Bookstore
{
    private ?int $id;
    private string $name;
    private string $city;
    private string $address;
    private ?string $phone;
    private ?string $email;
    private ?int $establishedYear;
    private ?float $squareMeters;
    private bool $hasCafe;
    private ?float $rating;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id,
        string $name,
        string $city,
        string $address,
        ?string $phone = null,
        ?string $email = null,
        ?int $establishedYear = null,
        ?float $squareMeters = null,
        bool $hasCafe = false,
        ?float $rating = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->city = $city;
        $this->address = $address;
        $this->phone = $phone;
        $this->email = $email;
        $this->establishedYear = $establishedYear;
        $this->squareMeters = $squareMeters;
        $this->hasCafe = $hasCafe;
        $this->rating = $rating;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getCity(): string { return $this->city; }
    public function getAddress(): string { return $this->address; }
    public function getPhone(): ?string { return $this->phone; }
    public function getEmail(): ?string { return $this->email; }
    public function getEstablishedYear(): ?int { return $this->establishedYear; }
    public function getSquareMeters(): ?float { return $this->squareMeters; }
    public function hasCafe(): bool { return $this->hasCafe; }
    public function getRating(): ?float { return $this->rating; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setName(string $name): void { $this->name = $name; }
    public function setCity(string $city): void { $this->city = $city; }
    public function setAddress(string $address): void { $this->address = $address; }
    public function setPhone(?string $phone): void { $this->phone = $phone; }
    public function setEmail(?string $email): void { $this->email = $email; }
    public function setEstablishedYear(?int $year): void { $this->establishedYear = $year; }
    public function setSquareMeters(?float $squareMeters): void { $this->squareMeters = $squareMeters; }
    public function setHasCafe(bool $hasCafe): void { $this->hasCafe = $hasCafe; }
    public function setRating(?float $rating): void { $this->rating = $rating; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'city' => $this->city,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'established_year' => $this->establishedYear,
            'square_meters' => $this->squareMeters,
            'has_cafe' =>  ($this->hasCafe === true ? 'true' : 'false'),
            'rating' => $this->rating,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}