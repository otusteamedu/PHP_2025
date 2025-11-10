<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Validator\EmailVerifier;

header('Content-Type: application/json');

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Only allow POST requests
if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed. Use POST.']);
    exit;
}

// Get input
$input = $_POST ? : json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['emails']) || !is_array($input['emails'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input. Please provide an array of emails.']);
    exit;
}

try {
    $verifier = new EmailVerifier();
    $results = $verifier->verifyEmails($input['emails']);
    
    echo json_encode([
        'success' => true,
        'data' => $results
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ]);
}