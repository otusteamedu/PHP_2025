<?php

namespace Arlex2305k\EmailsVerifier;

class Email
{
	private string $address;
	private array $errors;
	private ?string $localPart;
	private ?string $domain;

	public function __construct(string $address)
	{
		$this->address = trim($address);
		$this->errors = [];
		$this->localPart = $this->domain = null;
		if (str_contains($address, '@')) {
			$parts = explode('@', $address);
			if (count($parts) == 2) {
				$this->localPart = $parts[0];
				$this->domain = $parts[1];
			} else {
				$this->addError('В e-mail адресе более одного символа @');
			}
		} else {
			$this->addError('В e-mail адресе нет символа @');
		}
	}

	public function getAddress(): string
	{
		return $this->address;
	}

	public function getLocalPart(): ?string
	{
		return $this->localPart;
	}

	public function getDomain(): ?string
	{
		return $this->domain;
	}

	public function hasErrors(): bool
	{
		return count($this->errors) > 0;
	}

	public function addError(string $error): void
	{
		$this->errors[] = $error;
	}

	public function getErrors(): array
	{
		return $this->errors;
	}

	public function __toString(): string
	{
		return $this->address;
	}
}
