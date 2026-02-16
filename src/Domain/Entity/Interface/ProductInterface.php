<?php

namespace App\Domain\Entity\Interface;

interface ProductInterface
{
    public function getName(): string;

    public function getIngredients(): array;

    public function getCookingStatus(): string;

    public function setCookingStatus(string $status): void;

    public function isMeetsStandard(): bool;
}
