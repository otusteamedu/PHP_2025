<?php

namespace Arlex2305k\Brackets;

class Service
{
	private const RESPONSE_SUCCESS = 200;
	private const RESPONSE_ERROR = 400;
	private const RESPONSE_METHOD_NOT_ALLOWED = 405;

	public string $resultMessage;

	public function process(): int
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			$this->resultMessage = 'Method is not allowed';
			return self::RESPONSE_METHOD_NOT_ALLOWED;
		}

		$param = $_POST['string'] ?? null;

		if ($param === null) {
			$this->resultMessage = 'Missing required parameter: string';
			return self::RESPONSE_ERROR;
		}

		if (empty(trim($param))) {
			$this->resultMessage = 'String cannot be empty';
			return self::RESPONSE_ERROR;
		}

		if (!$this->isValid($param)) {
			$this->resultMessage = 'String has unbalanced brackets';
			return self::RESPONSE_ERROR;
		}

		$this->resultMessage = 'String is valid';
		return self::RESPONSE_SUCCESS;
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
