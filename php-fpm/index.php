<?php
ini_set('session.save_handler', 'redis');
ini_set('session.save_path', 'tcp://redis:6379?timeout=2&read_timeout=2&persistent=1');

session_start();

header('Content-Type: text/plain');

require_once 'autoload.php';

use App\Application;

$app = new Application();
$res = $app->run();

http_response_code($res['code']);
echo $res['message'];