<?php
declare(strict_types=1);


namespace App\User\Infrastructure\Database;

use App\App;
use PDO;

class Db
{
    public PDO $connection {
        get {
            return $this->connection;
        }
    }

    public function __construct()
    {
        $dsn = $this->buildDsn();
        $user = App::$app->getConfigValue('user');
        if (!is_string($user)) {
            throw new \Exception('user must be string');
        }
        $pass = App::$app->getConfigValue('pass');
        if (!is_string($pass)) {
            throw new \Exception('pass must be string');
        }
        $this->connection = new PDO($dsn, $user, $pass);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    private function buildDsn(): string
    {
        $driver = $this->parseDriver();
        $host = App::$app->getConfigValue('host');

        if (!is_string($host)) {
            throw new \Exception('host must be string');
        }
        $port = App::$app->getConfigValue('port');
        if (!is_numeric($port)) {
            throw new \Exception('port must be numeric');
        }
        $dbname = App::$app->getConfigValue('dbname');
        if (!is_string($dbname)) {
            throw new \Exception('dbname must be string');
        }

        return "$driver:host=$host;port=$port;dbname=$dbname";
    }

    private function parseDriver(): string
    {
        $driver = getenv('DB_DRIVER');

        return match ($driver) {
            'pdo_pgsql' => 'pgsql',
            default => 'mysql',
        };
    }


}