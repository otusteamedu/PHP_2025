<?php
session_start();

if (!isset($_SESSION['views'])) {
    $_SESSION['views'] = 0;
}

$_SESSION['views']++;

header('Content-type: application/json');
$response[0] = [
    'sessionViews' => $_SESSION['views'],
    'upstreamIP'=> $_SERVER['SERVER_ADDR'],
];

echo json_encode($response);