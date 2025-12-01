<?php

namespace Arlex2305k\EmailsVerifier;

class Validator
{
	private function checkFormat(Email $email): bool
	{
		$result = true;

		$address = $email->getAddress();

		static $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
		if (!preg_match($pattern, $address)) {
			$email->addError('Адрес не соответствует шаблону');
			$result = false;
		}

		if (str_contains($address, '..')) {
			$email->addError('В адресе найдены две точки подряд');
			$result = false;
		}

		$localPart = $email->getLocalPart();
		if (str_starts_with($localPart, '.') || str_ends_with($localPart, '.')) {
			$email->addError('Локальная часть адреса начинается или заканчивается точкой');
			$result = false;
		}

		if (str_starts_with($email->getDomain(), '.')) {
			$email->addError('Домен начинается с точки');
			$result = false;
		}

		return $result;
	}

	private function checkMxRecord(Email $email): bool
	{
		$result = checkdnsrr($email->getDomain());
		if (!$result) {
			$email->addError('MX-запись не найдена');
		}
		return $result;
	}

	public function validate(Email $email): bool
	{
		if ($email->hasErrors()) { // это вообще не e-mail-адрес
			return false;
		}
		if (!$this->checkFormat($email)) { // ошибки формата - нет смысла проверять MX-запись
			return false;
		}
		$this->checkMxRecord($email);
		return !$email->hasErrors();
	}
}
