<?php declare(strict_types=1);

class Solution
{

	/**
	 * @param Integer[] $nums
	 * @return Integer[]
	 */
	function frequencySort($nums)
	{
		$counted = array_count_values($nums);

		usort($nums, function ($key1, $key2) use ($counted)
		{
			if ($counted[$key1] !== $counted[$key2])
			{
				return $counted[$key1] <=> $counted[$key2];
			} else {
				return $key2 <=> $key1;
			}
		});

		return $nums;
	}
}

print_r((new Solution())->frequencySort([1,1,2,2,2,3]));
print_r((new Solution())->frequencySort([2,3,1,3,2]));
print_r((new Solution())->frequencySort([-1,1,-6,4,5,-6,1,4,1]));