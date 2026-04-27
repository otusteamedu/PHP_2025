<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Services\QueueService;
use App\Services\RequestStatusService;

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$path = rtrim($path, '/');
$path = $path === '' ? '/' : $path;

if ($method === 'GET' && $path === '/') {
    sendJson(200, [
        'service' => 'async-request-api',
        'version' => '1.0.0',
        'documentation' => '/openapi.yaml',
        'endpoints' => [
            'POST /api/requests',
            'GET /api/requests/{request_id}',
        ],
    ]);
}

if ($method === 'POST' && $path === '/api/requests') {
    createRequest();
}

if ($method === 'GET' && preg_match('#^/api/requests/([A-Za-z0-9_-]+)$#', $path, $matches)) {
    getRequestStatus($matches[1]);
}

sendJson(404, [
    'error' => 'Route not found.',
]);

function createRequest(): void
{
    $payload = readJsonBody();
    $errors = validatePayload($payload);

    if (!empty($errors)) {
        sendJson(422, [
            'error' => 'Validation failed.',
            'details' => $errors,
        ]);
    }

    $requestId = bin2hex(random_bytes(16));

    $requestPayload = [
        'type' => 'statement_generation',
        'date_from' => $payload['date_from'],
        'date_to' => $payload['date_to'],
        'email' => $payload['email'],
    ];

    try {
        $statusService = new RequestStatusService();
        $record = $statusService->create($requestId, $requestPayload);

        $task = $requestPayload;
        $task['request_id'] = $requestId;
        $task['created_at'] = $record['created_at'];

        $queueService = new QueueService();
        $queueService->push($task);
    } catch (Throwable $e) {
        if (isset($statusService)) {
            try {
                $statusService->markFailed($requestId, 'Failed to enqueue request: ' . $e->getMessage());
            } catch (Throwable $inner) {
                // Игнорируем вторичные ошибки во время обработки сбоя постановки в очередь.
            }
        }

        sendJson(503, [
            'error' => 'Unable to enqueue request at the moment.',
            'details' => $e->getMessage(),
        ]);
    }

    sendJson(202, [
        'request_id' => $requestId,
        'status' => 'queued',
        'status_url' => '/api/requests/' . $requestId,
        'created_at' => $record['created_at'],
    ]);
}

function getRequestStatus(string $requestId): void
{
    try {
        $statusService = new RequestStatusService();
        $record = $statusService->get($requestId);
    } catch (InvalidArgumentException $e) {
        sendJson(400, [
            'error' => $e->getMessage(),
        ]);
    } catch (Throwable $e) {
        sendJson(500, [
            'error' => 'Failed to read request status.',
            'details' => $e->getMessage(),
        ]);
    }

    if ($record === null) {
        sendJson(404, [
            'error' => 'Request not found.',
        ]);
    }

    $response = [
        'request_id' => $record['request_id'],
        'status' => $record['status'],
        'created_at' => $record['created_at'],
        'updated_at' => $record['updated_at'],
    ];

    if (isset($record['started_at'])) {
        $response['started_at'] = $record['started_at'];
    }

    if (isset($record['completed_at'])) {
        $response['completed_at'] = $record['completed_at'];
    }

    if (!empty($record['result'])) {
        $response['result'] = $record['result'];
    }

    if (!empty($record['error'])) {
        $response['error'] = $record['error'];
    }

    sendJson(200, $response);
}

function readJsonBody(): array
{
    $rawBody = file_get_contents('php://input');

    if ($rawBody === false || trim($rawBody) === '') {
        return [];
    }

    $decoded = json_decode($rawBody, true);

    if (!is_array($decoded)) {
        sendJson(400, [
            'error' => 'Invalid JSON body.',
        ]);
    }

    return $decoded;
}

function validatePayload(array $payload): array
{
    $errors = [];

    if (empty($payload['date_from']) || !isValidDate((string) $payload['date_from'])) {
        $errors['date_from'] = 'date_from is required in Y-m-d format.';
    }

    if (empty($payload['date_to']) || !isValidDate((string) $payload['date_to'])) {
        $errors['date_to'] = 'date_to is required in Y-m-d format.';
    }

    if (!empty($payload['date_from']) && !empty($payload['date_to']) && isValidDate((string) $payload['date_from']) && isValidDate((string) $payload['date_to'])) {
        if ($payload['date_from'] > $payload['date_to']) {
            $errors['date_range'] = 'date_from must be less than or equal to date_to.';
        }
    }

    if (empty($payload['email']) || !filter_var((string) $payload['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'email is required and must be valid.';
    }

    return $errors;
}

function isValidDate(string $value): bool
{
    $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);

    return $date !== false && $date->format('Y-m-d') === $value;
}

function sendJson(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}
