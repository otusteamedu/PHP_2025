<?php
session_start();

if (!isset($_SESSION['views'])) {
    $_SESSION['views'] = 0;
}

$_SESSION['views']++;

$server_ip = $_SERVER['SERVER_ADDR'];
echo "This is response from Nginx Server $server_ip. Session views: {$_SESSION['views']}";
