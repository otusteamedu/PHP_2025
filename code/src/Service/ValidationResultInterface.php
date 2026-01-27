<?php

namespace App\Service;

interface ValidationResultInterface
{

	const OK_REQUEST = 200;

	public function getMessage(): string;

	public function getCode(): int;

}