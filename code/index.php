<?php
require __DIR__ . '/vendor/autoload.php';

use Ak\Hw\Services\BracketsBalance;

$string = htmlspecialchars(htmlspecialchars($_REQUEST['string']) ?? '');

if ($string) {
    try {
        $message = new BracketsBalance()($string)['message'];
        $code = 200;
    } catch (Exception $e) {
        $message = $e->getMessage();
        $code = $e->getCode();
    }
    http_response_code($code);
    header('Content-Type: application/text; charset=utf-8');
    echo $message;
}
?>