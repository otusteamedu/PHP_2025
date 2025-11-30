<?php

namespace Arlex2305k\EmailsVerifier;

class Validator
{
	private static function checkFormat(string $email): bool
	{
		static $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
		if (!preg_match($pattern, $email)) {
			return false;
		}

		if (str_contains($email, '..')) {
			return false;
		}

		$parts = explode('@', $email);
		$localPart = $parts[0];
		$domain = $parts[1] ?? '';
		if (str_starts_with($localPart, '.') || str_ends_with($localPart, '.')) {
			return false;
		}
		if (str_starts_with($domain, '.')) {
			return false;
		}

		return true;
	}

	private static function checkMxRecord(string $email): bool
	{
		$domain = substr(strrchr($email, "@"), 1);
		return checkdnsrr($domain);
	}

	public static function validate(string $email): bool
	{
		if (!self::checkFormat($email)) {
			return false;
		}

		if (!self::checkMxRecord($email)) {
			return false;
		}

		return true;
	}
}
