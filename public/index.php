<?php
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

if ($request_uri === '/api/submit' && $request_method === 'POST') {
    $controller = new App\Controllers\JobController();
    $controller->submit();
} elseif (preg_match('/^\/api\/status\/(\d+)$/', $request_uri, $matches) && $request_method === 'GET') {
    $controller = new App\Controllers\JobController();
    $controller->status($matches[1]);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found']);
}