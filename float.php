<?php

$dsn = 'pgsql:host=192.168.1.2;port=5432;dbname=otus';

$user = 'otus';
$password = 'otus';

$pdo = new PDO($dsn, $user, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

echo $pi = M_PI, PHP_EOL;

$pdo->exec('update movie_entity_values set value_double = ' . $pi . ' where id = 19;');

[
    'value_double' => $value,
] = $pdo->query('select value_double from movie_entity_values where id = 19')->fetch();

echo $value, PHP_EOL;

// 3.1415926535898
// 3.1415926535898
