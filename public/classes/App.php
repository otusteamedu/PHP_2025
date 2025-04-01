<?php

namespace classes;

use classes\DataMapper\Config;
use classes\DataMapper\FilmMapper;



class App
{
    private $pdoConnection;
    private $filmMapper;
    private const FILMS_LIMIT = 100;

    public function __construct()
    {
         try {
            $appConfig = Config::getPDOParams();

            $host = $appConfig['HOST'];
            $db = $appConfig['DB'];
            $user = $appConfig['USER'];
            $password = $appConfig['PASSWORD'];

            $dsn = "pgsql:host=$host;port=5432;dbname=$db;";
            $pdoConnection = new \PDO($dsn, $user, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);

            if ($pdoConnection) {
                $this->pdoConnection = $pdoConnection;
                $this->filmMapper = new FilmMapper($this->pdoConnection);
            }
        } catch (\PDOException $e) {
            die($e->getMessage());
        }
    }

    public function run()
    {
        //$this->createTable();
        //$this->fillTheTable();
        return $this->getLazyFilmsCollection();
    }

    private function getLazyFilmsCollection()
    {
        static $filmsCollection;
        if (is_null($filmsCollection)) {
            $filmsCollection = $this->filmMapper->getMany(self::FILMS_LIMIT);
        }
        return $filmsCollection;
    }

    private function fillTheTable():void
    {
        $rawUserData = [];

        for ($i = 1; $i < 100; $i++) {
            $postfix = rand(1, 5000);
            $rating = rand(50, 400);
            $rawUserData['title'] = 'test_'.$postfix;
            $rawUserData['code'] = 'test_code_'.$postfix;
            $rawUserData['rating'] = $rating;
            $this->filmMapper->insert($rawUserData);
        }
    }

    private function createTable():void
    {
        $query = "CREATE TABLE IF NOT EXISTS films (
                id serial PRIMARY KEY,
                title varchar(80),
                code varchar(80),
                rating integer
        );";

        $this->pdoConnection->exec($query);
    }

}