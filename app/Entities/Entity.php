<?php

namespace App\Entities;

class Entity
{
    /**
     * @return array|null
     */
    public function toArray(): ?array {
        return array_filter((array)$this);
    }
}