<?php declare(strict_types=1);

class Solution
{

	/**
	 * @param Integer[] $nums
	 * @return Integer[]
	 */
	function smallerNumbersThanCurrent($nums)
	{
		$hash = $nums;
		sort($hash);

		for ($i = 0; $i < count($hash); $i++)
		{
			$nums[$i] = array_search($nums[$i], $hash);
		}

		return $nums;
	}
}

print_r((new Solution())->smallerNumbersThanCurrent([8,1,2,2,3]));
print_r((new Solution())->smallerNumbersThanCurrent([6,5,4,8]));
print_r((new Solution())->smallerNumbersThanCurrent([7,7,7,7]));