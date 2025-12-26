<?php
header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

$string = $_POST['string'] ?? '';

if (empty($string)) {
    http_response_code(400);
    echo 'Bad Request: String is empty';
    exit;
}

function isBalanced($str) {
    $stack = 0;
    for ($i = 0; $i < strlen($str); $i++) {
        $char = $str[$i];
        if ($char === '(') {
            $stack++;
        } elseif ($char === ')') {
            $stack--;
            if ($stack < 0) {
                return false;
            }
        } else {
            // Если есть другие символы кроме скобок
            return false;
        }
    }
    return $stack === 0;
}

if (isBalanced($string)) {
    http_response_code(200);
    echo 'OK: String is balanced';
} else {
    http_response_code(400);
    echo 'Bad Request: String is not balanced';
}