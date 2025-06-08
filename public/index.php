<?php
require  __DIR__ . '/../vendor/autoload.php';
ob_start();

try {

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        throw new \App\Exception\HttpException('Запрос должен быть типа POST');
    }

    if (isset($_POST['string']))
    {
        $string = $_POST['string'];
    } elseif(file_get_contents('php://input'))
    {
        $string = json_decode(file_get_contents('php://input'), true);
    }

    if (empty($string))
    {
        throw new \App\Exception\HttpException('Параметр string является обязательным');
    }

    $status = true;

    if (!\App\StringValidator::validateExternalSymbols($string)) {
        throw new \App\Exception\ValidateException('Строка содержит лишние символы');
    }

    if (!\App\StringValidator::isStaplesValid($string)) {
        throw new \App\Exception\ValidateException('Строка некорретная. Присутствуют лишние символы');
    } else {
        $message = 'Строка корректна';
    }

} catch (\App\Exception\HttpException | \App\Exception\ValidateException $e) {
    $status = false;
    $message = $e->getMessage();
} catch (\Exception $e) {
    $status = false;
    $message = 'Произошла ошибка';
}

ob_end_clean();
header('Content-Type: application/json');
http_response_code($status ? 200 : 400);
echo json_encode([
    'message' => $message,
]);

