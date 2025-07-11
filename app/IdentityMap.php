<?php declare(strict_types=1);

namespace DMapper;

class IdentityMap
{
	private $identityMap = [];

	public function add($object)
	{
		$key = $this->getKey(get_class($object), $object->getId());
		$this->identityMap[$key] = $object;
	}

	public function get($className, $id)
	{
		$key = $this->getKey($className, $id);
		return $this->identityMap[$key] ?? null;
	}

	public function has($className, $id)
	{
		$key = $this->getKey($className, $id);
		return isset($this->identityMap[$key]);
	}

	private function getKey($className, $id)
	{
		return sprintf('%s.%d', $className, $id);
	}
}