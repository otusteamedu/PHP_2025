<?php
require_once __DIR__ . '/vendor/autoload.php';

use src\BracketValidator;

ini_set('session.save_handler', 'redis');
ini_set('session.save_path', 'tcp://app-redis:6379');
session_start();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Только POST запросы", 405);
    }
    $input = $_POST['string'] ?? '';
    $validator = new BracketValidator();
    $validator->validate($input);
    http_response_code(200);
    echo json_encode([
        'status' => 'ok',
        'msg' => 'Строка валидна',
        'server' => gethostname()
    ]);

} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
}
