<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['string']) || empty($_POST['string'])) {
        http_response_code(400);
        echo "String is empty.";
        exit;
    }

    $string = $_POST['string'];
    if (isValidParentheses($string)) {
        http_response_code(200);
        echo "String is valid.";
    } else {
        http_response_code(400);
        echo "String is invalid.";
    }
}

function isValidParentheses($string) {
    $stack = [];
    $pairs = ['(' => ')'];

    foreach (str_split($string) as $char) {
        if (isset($pairs[$char])) {
            $stack[] = $char;
        } elseif (in_array($char, $pairs)) {
            if (empty($stack) || $pairs[array_pop($stack)] !== $char) {
                return false;
            }
        }
    }

    return empty($stack);
}