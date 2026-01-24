<?php

namespace App\Service;

interface ValidatorInterface{

	public function validate(string $input): ValidationResult;
}