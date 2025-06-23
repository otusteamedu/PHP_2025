<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Пример использования
use App\Validator\EmailVerifier;

$verifier = new EmailVerifier();

// Список email для проверки
$emailsToCheck = [
    'test@example.com',
    'invalid.email',
    'missing@domain.nonexistenttld',
    'user@gmail.com',
    'another.user@yahoo.com',
    'no.mx.records@thisdomainsurelydoesnotexist12345.com'
];

// Проверяем email
$results = $verifier->verifyEmails($emailsToCheck);

// Выводим результаты
echo "<h2>Email Verification Results</h2>" . PHP_EOL;
echo "<table border='1'>";
echo "<tr><th>Email</th><th>Valid</th><th>Reason</th><th>Regex Check</th><th>MX Check</th></tr>";

foreach ($results as $email => $result) {
    echo "<tr>";
    echo "<td>$email</td>";
    echo "<td>" . ($result['valid'] ? 'Yes' : 'No') . "</td>";
    echo "<td>{$result['reason']}</td>";
    echo "<td>" . ($result['regex_check'] ? 'Passed' : 'Failed') . "</td>";
    echo "<td>" . ($result['mx_check'] ? 'Passed' : ($result['mx_check'] === null ? 'N/A' : 'Failed')) . "</td>";
    echo "</tr>";
}

echo "</table>";