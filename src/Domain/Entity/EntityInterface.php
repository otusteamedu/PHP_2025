<?php

namespace App\Domain\Entity;

interface EntityInterface
{
    public function getId(): ?int;

    public function toArray(): array;
}
