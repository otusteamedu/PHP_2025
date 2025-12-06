<?php

namespace Arlex2305k\MergeLists;

class ListNode
{
	private const MIN = -100;
	private const MAX = 100;
	public $val;
	public $next;

	function __construct(int $val = 0, ?ListNode $next = null)
	{
		if ($val < self::MIN || $val > self::MAX) {
			throw new \InvalidArgumentException("Значение должно быть в диапазоне от " . self::MIN . " до " . self::MAX);
		}
		$this->val = $val;
		$this->next = $next;
	}

	public function __toArray(): array
	{
		$result = [];
		$currentNode = $this;
		do {
			$result[] = $currentNode->val;
			$currentNode = $currentNode->next;
		} while ($currentNode !== null);
		return $result;
	}

	public function __toString(): string
	{
		return implode(', ', $this->__toArray());
	}
}
