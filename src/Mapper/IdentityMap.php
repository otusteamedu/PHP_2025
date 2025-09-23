<?php

declare(strict_types=1);

namespace Dinargab\Homework12\Mapper;

class IdentityMap
{
    private array $objects = [];


    public function setObject($object, $id): void
    {
        $this->objects[$this->getObjectKey($object, $id)] = $object;
    }

    public function hasId($className, $id)
    {
        return isset($this->objects[$className . $id]);
    }

    public function getObject($className, $id)
    {
        if ($this->hasId($className, $id)) {
            return $this->objects[$className . $id];
        }
        return null;
    }

    public function deleteObject($className, $id)
    {
        if (in_array($className . $id, array_keys($this->objects))) {
            unset($this->objects[$className . $id]);
        }
    }

    private function getObjectKey($object, $id)
    {
        return get_class($object) . $id;
    }

}
