<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ParenthesisValidator.php';

if (isset($_REQUEST['test']) && $_REQUEST['test'] == 'Y') {
    ParenthesisValidator::runTests();
    die();
}