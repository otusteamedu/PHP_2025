<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ParenthesisValidator.php';

if (isset($_REQUEST['test']) && $_REQUEST['test'] == 'Y') {
    ParenthesisValidator::runTests();
    die();
}

$actionValue = $_REQUEST['string'] ?? '';

$parenthesisValidator = new ParenthesisValidator($actionValue);
try {
    $parenthesisValidator->validate();
    echo 'OK';
} catch (Exception $e) {
    http_response_code(400);
    echo $e->getMessage();
}