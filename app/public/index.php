<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Process;

$results = Process::processEmails(__DIR__ . '/../emails.txt');

header('Content-Type: application/json; charset=utf-8');
echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>