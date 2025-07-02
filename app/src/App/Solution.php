<?php

declare(strict_types=1);

namespace SergeyGolovanov\Php2025Hw7\App;

use SergeyGolovanov\Php2025Hw7\Domain\Node;
use SergeyGolovanov\Php2025Hw7\Domain\NodeList;

class Solution
{

	private NodeList $resultList;

	public function __construct()
	{
		$this->resultList = new NodeList();
	}

	/**
	 * @param NodeList $list1
	 * @param NodeList $list2
	 * @return ?Node
	 */
	function mergeTwoLists($list1, $list2): ?Node
	{
		/**
		 * Если оба списка пустые
		 */
		if ($list1->getHead() === null && $list2->getHead() === null) {
			return $this->resultList->getHead();
		}

		/**
		 * Один из списков пустой
		 */
		if ($list1->getHead() === null || $list2->getHead() === null) {
			$this->resultList->setHead($list1->getHead() ?? $list2->getHead());
			return $this->resultList->getHead();
		}

		$currentPointerList1 = $list1->getHead();
		$currentPointerList2 = $list2->getHead();

		do {
			/**
			 * Если первый список закончился, а второй нет
			 * или второй список не Null и значение из первого списка больше значения из второго
			 * тогда в результ список добавляем элемент из второго списка
			 * */
			if (($currentPointerList1 === null && $currentPointerList2 !== null)
				|| ($currentPointerList2 !== null && $currentPointerList1->val >= $currentPointerList2->val)
			) {
				$this->resultList->append($currentPointerList2->val);
				$currentPointerList2 = $currentPointerList2->next;
			} elseif (($currentPointerList2 === null && $currentPointerList1 !== null)
				|| ($currentPointerList1 !== null && $currentPointerList1->val < $currentPointerList2->val)
			) {
				/**
				 * Иначе,
				 * если второй список закончился, а первый нет
				 * или первый список не Null и значение из второго списка больше значения из первого
				 * в результ список добавляем элемент из первого списка
				 */
				$this->resultList->append($currentPointerList1->val);
				$currentPointerList1 = $currentPointerList1->next;
			}

		} while (!($currentPointerList1 === null && $currentPointerList2 === null));

		return $this->resultList->getHead();
	}
}
