<?php

$host = getenv('DB_HOST');
$port = 5432;
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

$message = "POSTGRES CONNECTION - ";

if (@pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password")) {
    $message .= "OK!\r\n";
} else {
    $message .= "FAIL!\r\n";
}

echo $message;