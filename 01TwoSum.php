<?php declare(strict_types=1);

class Solution
{

	/**
	 * @param Integer[] $nums
	 * @param Integer $target
	 * @return Integer[]
	 */
	function twoSum($nums, $target)
	{
		$hash = [];

		foreach ($nums as $i => $num)
		{
			if (isset($hash[$num])) return [$hash[$num], $i];

			$hash[$target - $num] = $i;
		}

		return [];
	}

}

print_r((new Solution())->twoSum([2, 7, 11, 15], 9));
print_r((new Solution())->twoSum([3, 2, 4], 6));
print_r((new Solution())->twoSum([3, 3], 6));