<?php
require __DIR__ . '/../vendor/autoload.php';

use App\EmailTester;

$tester = new EmailTester();
$results = $tester->testEmails();

echo "=== Результаты проверки email ===\n";
$tester->printResults($results);

echo "\n=== Проверка дополнительных email ===\n";
$customResults = $tester->testEmails([
    "myemail@gmail.com",
    "invalid@domain",
    "test@yahoo.com"
]);
$tester->printResults($customResults);