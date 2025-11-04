<?php

namespace Blarkinov\PhpDbCourse\Service\Database;

use Exception;

class Fabric
{

    private const TYPE_MYSQL = "mysql";

    public function create()
    {
        switch ($_ENV['DATABASE_TYPE']) {
            case self::TYPE_MYSQL:
                return new MySQL();
            default:
                throw new Exception('unknown type database');
        }
    }
}
