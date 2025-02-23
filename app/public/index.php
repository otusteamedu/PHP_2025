<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Request method must be POST.');
    }
    $requestBody = json_decode(file_get_contents('php://input'), true);
    if (!$requestBody) {
        throw new Exception('Invalid JSON request body.');
    }
    $emails = $requestBody['emails'] ?? null;
    if (!$emails && !is_array($emails)) {
        throw new Exception('Invalid body format.');
    }
    $valid = [];
    $invalid = [];
    foreach ($emails as $email) {
        if (isValidEmail($email) && hasMxRecord($email)) {
            $valid[] = $email;
            continue;
        }
        $invalid[] = $email;
    }

    echo jsonResponse(compact('valid', 'invalid'), null);

} catch (Throwable $e) {
    echo jsonResponse(null, $e->getMessage(), 400);
}

function jsonResponse(?array $data, ?string $message, int $httpStatusCode = 200): string
{
    http_response_code($httpStatusCode);
    header('Content-Type: application/json; charset=utf-8');
    $result = [
        'result' => getResult($httpStatusCode),
        'status' => $httpStatusCode,
        'data' => $data,
        'message' => $message,

    ];
    return json_encode($result);
}

function getResult(int $httpStatusCode): string
{
    return match (true) {
        $httpStatusCode >= 200 && $httpStatusCode < 400 => 'success',
        default => 'error',
    };
}

function isValidEmail(string $email): bool
{
    return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
}

function hasMxRecord(string $email): bool
{
    return !empty(dns_get_record(explode('@', $email)[1], DNS_MX));
}