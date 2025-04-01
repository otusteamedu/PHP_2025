<?php

namespace classes;

use classes\DataMapper\FilmMapper;

class App
{

    public function run()
    {
        //$conn = new \PDO("pgsql:host=localhost;port=5496;dbname=patterns", "pavel", "rightway");

        //СОЗДАНИЕ ТАБЛИЦЫ films

        try {
            $host= 'pgsql';
            $db = 'patterns';
            $user = 'pavel';
            $password = 'rightway'; // change to your password

            $dsn = "pgsql:host=$host;port=5432;dbname=$db;";
            // make a database connection
            $pdoConnection = new \PDO($dsn, $user, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);

            if ($pdoConnection) {
                echo "Connected to the $db database successfully!";
            }
        } catch (\PDOException $e) {
            die($e->getMessage());
        }

//
//        $sql = "CREATE TABLE films (
//                id serial PRIMARY KEY,
//                title varchar(80),
//                code varchar(80),
//                rating integer
//        );";
//
//        var_dump($pdoConnection);
//
//
//        // выполняем SQL-выражение
//        $result = $pdoConnection->exec($sql);
//
//        var_dump($result);




        $filmMapper = new FilmMapper($pdoConnection);
        $rawUserData = [];
        $rawUserData['title'] = 'test';
        $rawUserData['code'] = 'test_code';
        $rawUserData['rating'] = 44;


        $res = $filmMapper->insert($rawUserData);

        var_dump($res);

        echo 'RUN!!!!';
    }

}