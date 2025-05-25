<?php
ini_set('session.save_handler', 'redis');
ini_set('session.save_path', 'tcp://redis:6379?timeout=2&read_timeout=2&persistent=1');

session_start();

header('Content-Type: text/plain');

$logFile = '/var/log/requests.log';
$hostname = gethostname();
$requestTime = date('Y-m-d H:i:s');
$string = $_POST['string'] ?? '';

try {
    if (!isset($_SESSION['request_count'])) {
        $_SESSION['request_count'] = 0;
    }
    $_SESSION['request_count']++;
    $str = "[{$requestTime}] RequestCount: {$_SESSION['request_count']}\n";
    file_put_contents($logFile, $str, FILE_APPEND);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new \Exception('Method Not Allowed' . PHP_EOL);
    }

    if (empty($string)) {
        throw new \Exception('Bad Request: String is empty' . PHP_EOL);
    }

    $result = isBalanced($string);
    if (!$result) {
        throw new \Exception('Bad Request: String is not balanced' . PHP_EOL);
    }

    http_response_code(200);
    $response = 'OK: String is balanced' . PHP_EOL;
    $str = "[{$requestTime}] [Host: {$hostname}] Response: {$response}";
    file_put_contents($logFile, $str, FILE_APPEND);
    echo $response;

} catch (\Exception $e) {
    $str = "[{$requestTime}] [Host: {$hostname}] Error: {$e->getMessage()}";
    file_put_contents($logFile, $str, FILE_APPEND);
    http_response_code(400);
    echo $e->getMessage();
}

function isBalanced(string $str): bool {
    $balance = 0;
    for ($i = 0; $i < strlen($str); $i++) {
        if ($str[$i] === '(') {
            $balance++;
        } elseif ($str[$i] === ')') {
            $balance--;
            if ($balance < 0) {
                return false;
            }
        }
    }
    return $balance === 0;
}