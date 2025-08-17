<?php

ini_set('session.save_handler', 'redis');
ini_set('session.save_path', 'tcp://redis:6379');

session_start();

// Проверка CSRF-токена
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

class EmailVerifier {
    private const EMAIL_REGEX = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/';

    public function verifyEmails(array $emails): array {
        $results = [];
        foreach ($emails as $email) {
            $email = trim($email); // Удаляем пробелы
            $result = [
                'email' => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'), // Защита от XSS
                'is_valid' => false,
                'message' => ''
            ];

            try {
                // 1. Проверка синтаксиса с помощью регулярного выражения
                if (!$this->isValidSyntax($email)) {
                    $result['message'] = 'Invalid email format';
                    $results[] = $result;
                    continue;
                }

                // 2. Проверка MX-записей
                $domain = substr(strrchr($email, '@'), 1);
                if (!$this->hasValidMxRecord($domain)) {
                    $result['message'] = 'No valid MX record found for domain';
                    $results[] = $result;
                    continue;
                }

                $result['is_valid'] = true;
                $result['message'] = 'Email is valid';
            } catch (Exception $e) {
                $result['message'] = 'Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
            }

            $results[] = $result;
        }
        return $results;
    }

    private function isValidSyntax(string $email): bool {
        return preg_match(self::EMAIL_REGEX, $email) === 1;
    }

    private function hasValidMxRecord(string $domain): bool {
        return checkdnsrr($domain, 'MX');
    }
}

// Обработка запроса
try {
    // Проверка метода запроса
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        exit;
    }

    // Проверка CSRF-токена
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrf_token)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid CSRF token']);
        exit;
    }

    // Получение списка email
    $emails = $_POST['emails'] ?? [];
    if (!is_array($emails) || empty($emails)) {
        http_response_code(400);
        echo json_encode(['error' => 'No emails provided']);
        exit;
    }

    // Верификация email
    $verifier = new EmailVerifier();
    $results = $verifier->verifyEmails($emails);

    // Отправка ответа
    http_response_code(200);
    header('Content-Type: application/json');
    header('X-Container-Name: ' . gethostname()); // Для отладки балансировки
    echo json_encode($results);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')]);
}
?>