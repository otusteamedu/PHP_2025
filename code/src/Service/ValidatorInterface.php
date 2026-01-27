<?php

namespace App\Service;

interface ValidatorInterface
{
	const BAD_REQUEST = 400;
	const OK_REQUEST = 200;

	public function validate(string $input): ValidationResult;
}