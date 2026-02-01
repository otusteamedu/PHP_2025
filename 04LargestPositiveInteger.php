<?php declare(strict_types=1);

class Solution
{

	/**
	 * @param Integer[] $nums
	 * @return Integer
	 */
	function findMaxK($nums)
	{
		$out = -1;
		$hash = [];

		foreach ($nums as $num)
		{
			$absNum = abs($num);

            if ($absNum > $out && isset($hash[-$num])) $out = $absNum;

			$hash[$num] = false;
        }

		return $out;
	}
}

print_r((new Solution())->findMaxK([-1,2,-3,3]));
print_r((new Solution())->findMaxK([-1,10,6,7,-7,1]));
print_r((new Solution())->findMaxK([-10,8,6,7,-2,-3]));