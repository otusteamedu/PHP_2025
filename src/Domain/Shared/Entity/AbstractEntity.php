<?php

declare(strict_types=1);

namespace App\Domain\Shared\Entity;

abstract class AbstractEntity implements EntityInterface
{
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function toArray(): array
    {
        $reflectionProperties = new \ReflectionObject($this)->getProperties();

        $properties = [];
        foreach ($reflectionProperties as $reflectionProperty) {
            $properties[$reflectionProperty->getName()] = $reflectionProperty->getValue($this);
        }

        return array_replace(['id' => $this->getId()], $properties);
    }
}
