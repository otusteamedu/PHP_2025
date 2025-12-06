<?php

namespace Arlex2305k\MergeLists;

class Solution
{
	public function mergeTwoLists(?ListNode $list1, ?ListNode $list2): ?ListNode
	{
		if ($list1 === null) {
			return $list2;
		}
		if ($list2 === null) {
			return $list1;
		}

		if ($list1->val <= $list2->val) {
			$head = $list1;
			$list1 = $list1->next;
		} else {
			$head = $list2;
			$list2 = $list2->next;
		}
		
		$current = $head;
		
		while ($list1 !== null && $list2 !== null) {
			if ($list1->val <= $list2->val) {
				$current->next = $list1;
				$list1 = $list1->next;
			} else {
				$current->next = $list2;
				$list2 = $list2->next;
			}
			$current = $current->next;
		}
		
		$current->next = $list1 ?? $list2;
		
		return $head;
	}
}
