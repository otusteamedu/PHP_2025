<?php

namespace App\Service;

interface ValidationResultInterface{

	public function getMessage(): string;

	public function getCode(): int;

}