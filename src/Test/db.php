<?php

//Test DB connection
$servername = getenv('DB_SERVER');
$username = getenv('MYSQL_USER');
$dbname = getenv('MYSQL_DATABASE');
$password = getenv('MYSQL_PASSWORD');

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "Connected successfully";
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

