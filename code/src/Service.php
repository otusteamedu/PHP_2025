<?php

namespace Arlex2305k\Brackets;

class Service
{
	private const RESPONSE_SUCCESS = 200;
	private const RESPONSE_ERROR = 400;
	private const RESPONSE_METHOD_NOT_ALLOWED = 405;

	public function process(): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			http_response_code(self::RESPONSE_METHOD_NOT_ALLOWED);
			echo "Method is not allowed\n";
			return;
		}

		$param = $_POST['string'] ?? null;

		if ($param === null) {
			http_response_code(self::RESPONSE_ERROR);
			echo "Missing required parameter: string\n";
			return;
		}

		if (empty(trim($param))) {
			http_response_code(self::RESPONSE_ERROR);
			echo "String cannot be empty\n";
			return;
		}

		if ($this->isValid($param)) {
			http_response_code(self::RESPONSE_SUCCESS);
			echo "String is valid\n";
		} else {
			http_response_code(self::RESPONSE_ERROR);
			echo "String has unbalanced brackets\n";
		}
	}

	private function isValid(string $str): bool
	{
		$stack = [];
		for ($i = 0; $i < strlen($str); $i++) {
			$char = $str[$i];
			if ($char == '(')
				$stack[] = $char;
			else if ($char == ')') {
				if (empty($stack))
					return false;
				array_pop($stack);
			}
		}
		return empty($stack);
	}
}
