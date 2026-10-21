<?php

declare(strict_types=1);

namespace App\Core\Database\Entity;

abstract class AbstractEntity implements EntityInterface
{
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
