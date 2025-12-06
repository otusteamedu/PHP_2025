<?php

namespace Arlex2305k\MergeLists;

class ListHelper
{
	private const MAX_NODES = 50;

	public static function createFromArray(array $listValues): ?ListNode
	{
		if (count($listValues) > self::MAX_NODES) {
			throw new \InvalidArgumentException("Количество элементов списка не должно превышать " . self::MAX_NODES);
		}
		if (count($listValues) == 0) {
			return null;
		}
		
		$head = $prevNode = null;
		for ($i = count($listValues) - 1; $i >= 0; $i--) {
			if (!is_null($prevNode) && $prevNode->val < $listValues[$i]) {
				throw new \InvalidArgumentException("Элементы списка должны быть упорядочены по возрастанию");
			}
			$head = new ListNode($listValues[$i], $prevNode);
			$prevNode = $head;
		}
		
		return $head;
	}
}
