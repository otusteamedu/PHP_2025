<?php

namespace Arlex2305k\EmailsVerifier;

class Service
{
	private array $arEmails;

	private int $emailsCount, $validCount, $invalidCount;

	private Validator $validator;

	public function __construct(array $arEmails = [])
	{
		$this->setEmailsAsArray($arEmails);
		$this->initVars();
		$this->validator = new Validator();
	}

	private function initVars(): void
	{
		$this->arEmailsValid = $this->arEmailsInvalid = [];
		$this->validCount = $this->invalidCount = 0;
	}

	public function setEmailsAsArray(array $arEmails): Service
	{
		$this->arEmails = [];
		foreach ($arEmails as $email) {
			$this->arEmails[] = new Email($email);
		}
		$this->emailsCount = count($this->arEmails);
		return $this;
	}

	public function setEmailsAsString(string $emails): Service
	{
		$arEmails = [];
		if (str_contains($emails, ',')) {
			$arEmails = explode(',', $emails);
		} elseif (str_contains($emails, ';')) {
			$arEmails = explode(';', $emails);
		}
		$this->setEmailsAsArray($arEmails);
		return $this;
	}

	public function verify(): Service
	{
		$this->initVars();
		foreach ($this->arEmails as $email) {
			if ($this->validator->validate($email)) {
				$this->validCount++;
			} else {
				$this->invalidCount++;
			}
		}
		return $this;
	}

	public function __call(string $name, array $arguments): mixed
	{
		$nameOrig = $name;
		if (str_starts_with($name, 'get')) {
			$name = substr($name, 3);
			$name = strtolower($name[0]) . substr($name, 1);
			if (property_exists($this, $name)) {
				return $this->$name;
			}
		}
		throw new \ErrorException('Class ' . get_class($this) . ' does not contain the method ' . $nameOrig . PHP_EOL);
	}
}
