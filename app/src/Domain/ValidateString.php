<?php

declare(strict_types=1);

namespace SergeyGolovanov\HW4\Domain;

class ValidateString
{
	const OPEN_TAG = '(';
	const CLOSE_TAG = ')';

	public function isValid($incomData): bool
	{
		$string = $incomData ?? '';

		if (mb_strlen($string, 'UTF-8') < 1 || mb_strlen($string, 'UTF-8') % 2 !== 0) {
			return false;
		} else {
			$par = 0;
			for ($i = 0; $i < mb_strlen($string); $i++) {
				if ($string[$i] === self::OPEN_TAG) {
					++$par;
				} elseif ($string[$i] === self::CLOSE_TAG) {
					--$par;
				}

				if ($par < 0) {
					return false;
				}
			}
		}

		return true;
	}
}
