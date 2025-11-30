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
$service->verify();

$total = $service->getEmailsCount();
echo "Проверка Email [$total]:\n";
echo "==========================\n";

echo "--- Валидные [" . $service->getValidCount() . "]:\n";
foreach ($service->getArEmailsValid() as $email) {
	echo "$email : ok.\n";
}

echo "--- Невалидные [" . $service->getInvalidCount() . "]:\n";
foreach ($service->getArEmailsInvalid() as $email) {
	echo "$email : error!\n";
}
