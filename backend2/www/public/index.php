<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Validator\InputValidator;

$validator = new InputValidator();

try {
    $input = $_POST['string'] ?? '';
    $validator->validate($input);
    
    http_response_code(200);
    echo "Строка корректна\n";
} catch (Exception $e) {
    http_response_code(400);
    echo $e->getMessage() . "\n";
}
    
?>