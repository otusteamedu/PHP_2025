<?php

namespace App\Services;

use App\Interfaces\DatabaseInterface;

class MySQLDatabase implements DatabaseInterface {
    private $connection;

    public function __construct($host, $user, $pass, $name) {
        $this->connection = new \mysqli($host, $user, $pass, $name);
    }

    public function prepare(string $query) {
        return $this->connection->prepare($query);
    }
}
