<?php

require_once __DIR__ . '/vendor/autoload.php';

use Alisaselezneva\Code\Infrastructure\Http\Request;

header('Content-Type: application/json; charset=utf-8');

$request = new Request();
$request->handle();