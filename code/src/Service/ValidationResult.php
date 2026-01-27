<?php

namespace App\Service;

class ValidationResult implements ValidationResultInterface
{

	protected $message;
	protected $code;

	public function __construct(string $message, int $code)
	{
		$this->message = $message;
		$this->code = $code;
	}
	
	public function getMessage(): string
	{
		return $this->message;
	}

	public function getCode(): int
	{
		return $this->code;
	}

    public function isValid(): bool
    {
        return $this->code == self::OK_REQUEST;
    }	
}