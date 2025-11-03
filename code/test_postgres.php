<?php

$connection = pg_connect('
    host=' . $_ENV['POSTGRES_HOST'] . ' 
    port=' . $_ENV['POSTGRES_PORT'] .' 
    dbname=' . $_ENV['POSTGRES_DB'] . ' 
    user=' . $_ENV['POSTGRES_USER'] . ' 
    password=' . $_ENV['POSTGRES_PASSWORD']
);

$q = 'CREATE TABLE IF NOT EXISTS userdata (
"permission_id" BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
"username" VARCHAR NOT NULL
);';
pg_query($connection, $q);

$q = "INSERT INTO userdata (username) VALUES('Alisa');";  
pg_query($connection, $q);

$q = "SELECT * FROM userdata LIMIT 1;";  
$result = pg_query($connection, $q);
$data = pg_fetch_object($result);
echo $data->username;
