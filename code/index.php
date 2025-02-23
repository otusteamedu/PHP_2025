<?php
    
require_once 'BracketValidator.php';

//Тесты
//require_once 'BracketValidatorTest.php';

//Проверяем, был ли передан параметр string через POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['string'])) {
    $inputString = $_POST['string'];
    $validator = new BracketValidator($inputString);
    $isValid = $validator->isValid();
    
    if ($isValid) {
        http_response_code(200); // OK
        echo "Запрос корректный.";
    } else {
        http_response_code(400); // Bad Request
        echo "Запрос НЕ корректный. Валидация скобочек не пройдена.";
    }
} else {
    http_response_code(400); // Bad Request
    echo "Запрос НЕ корректный. Не POST запрос или в запросе нет параметра string";
}
?>

