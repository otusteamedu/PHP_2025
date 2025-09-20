<?php

declare(strict_types=1);

namespace Dinargab\Homework12\Mapper;

class IdentityMap
{
    private array $objects = [];


    public function setObject($id, $object): void
    {
        $this->objects[$id] = $object;
    }

    public function hasId($id)
    {
        return isset($this->objects[$id]);
    }

    public function getObject($id)
    {
        if ($this->hasId($id)) {
            return $this->objects[$id];
        }
        return null;
    }

    public function deleteObject($id)
    {
        if (in_array($id ,array_keys($this->objects))) {
            unset($this->objects[$id]);
        }
    }
}