<?php
namespace Ak\Hw\Mappers;

use ArrayObject;
use SplObjectStorage;
use OutOfBoundsException;

class IdentityMap
{
    public function __construct(
        protected ArrayObject $idToObject = new ArrayObject(),
        protected SplObjectStorage $objectToId = new SplObjectStorage()
    )
    {}

    public function set($id, $object): void
    {
        $this->idToObject[$id] = $object;
        $this->objectToId[$object] = $id;
    }

    public function getId(object $object): mixed
    {
        if (!$this->objectToId->offsetExists($object)) {
            throw new OutOfBoundsException("Object does not exist in the identity map.");
        }
        return $this->objectToId[$object];
    }

    public function getObject($id): mixed
    {
        if (!$this->hasId($id)) {
            throw new OutOfBoundsException("ID '$id' does not exist in the identity map.");
        }
        return $this->idToObject[$id];
    }

    public function hasId($id): bool
    {
        return isset($this->idToObject[$id]);
    }

    public function hasObject(object $object): bool
    {
        return $this->objectToId->offsetExists($object);
    }

    public function removeId($id): void
    {
        if ($this->hasId($id)) {
            $object = $this->idToObject[$id];
            unset($this->idToObject[$id]);
            if ($this->objectToId->offsetExists($object)) {
                $this->objectToId->offsetUnset($object);
            }
        }
    }
}