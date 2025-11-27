<?php
require_once 'InputValidator.php';

$validator = new Validator();

try {
    $validator->validate($_POST['string'] ?? '');
    http_response_code(200);
    echo "Строка корректна\n";
} catch (Exception $e) {
    http_response_code(400);
    echo $e->getMessage() . "\n";
}