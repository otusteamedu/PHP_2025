<?php

declare(strict_types=1);

namespace App\Domain\Shared\Entity;

interface EntityInterface
{
    public function getId(): ?int;

    public function toArray(): array;
}
