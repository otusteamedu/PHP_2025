<?php
require __DIR__ . '/vendor/autoload.php';

use Arlex2305k\EmailsVerifier\Service;

$emails = [
	// Валидные
	'user@example.com',
	'test.email@domain.org',
	'user+tag@example.net',
	'firstname.lastname@company.co.uk',
	'user123@test-domain.com',
	// Невалидные
	'invalid-email',
	'@invalid.com',
	'user@',
	'user..double.dot@example.com',
	'user@.com',
	'.user@domain.com',
	// Невалидные - MX
	'user@nonexistentdomain12345.com',
	'test@foolishdomain999.ru',
	'user@sub.domain.example.ru',
	'user_name@example.info',
];

$service = new Service($emails);
$result = $service->verify();

echo "Проверка Email [Всего: {$service->getEmailsCount()} | Валидны: {$service->getValidCount()} | Невалидны: {$service->getInvalidCount()}]:\n";
echo "==========================\n";

foreach ($service->getArEmails() as $email) {
	if (!$email->hasErrors()) {
		echo "$email : ok.\n";
	} else {
		$strErrors = implode(' | ', $email->getErrors());
		echo "$email : $strErrors\n";
	}
}
