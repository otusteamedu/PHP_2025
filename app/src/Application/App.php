<?php

namespace SergeyGolovanov\HW4\Application;

use SergeyGolovanov\HW4\Domain\ValidateString;
use Validate;

class App
{
	public function run(array $request)
	{
		$ruleValidate = new ValidateString();

		if ($ruleValidate->isValid($request['string'])) {
			$header_string = "HTTP/1.1 200 Ok";
			$return_code = 200;
		} else {
			$header_string = "HTTP/1.1 400 Bad Request";
			$return_code = 400;
		}

		return $this->response($header_string, true, $return_code);
	}

	private function response(string $header_string, bool $success, bool $return_code)
	{
		header($header_string, true, $return_code);
	}
}
