<?php declare(strict_types=1);

class Solution
{

	/**
	 * @param Integer[] $nums1
	 * @param Integer[] $nums2
	 * @return Integer[]
	 */
	function intersection($nums1, $nums2)
	{
		$flipNums1 = array_flip($nums1);
		$flipNums2 = array_flip($nums2);

		if (count($flipNums1) > count($flipNums2))
		{
			return $this->setIntersection($flipNums1, $flipNums2);
		} else {
			return $this->setIntersection($flipNums2, $flipNums1);
		}
	}

	function setIntersection($nums1, $nums2)
	{
		$out = [];

		$idx = 0;
		foreach ($nums1 as $key => $value)
		{
			if(isset($nums2[$key])) $out[$idx++] = $key;
		}

		return $out;
	}

}

print_r((new Solution())->intersection([1,2,2,1], [2,2]));
print_r((new Solution())->intersection([4,9,5], [9,4,9,8,4]));
print_r((new Solution())->intersection([7,9,5,5,3,2], [7,5,6,4,7,8]));