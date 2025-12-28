<?php

namespace Ak\Hw\Mappers;

use Ak\Hw\DB;
use PDO;

abstract class AbstractMapper
{
    protected PDO $db;
    protected IdentityMap $identityMap;

    public function __construct()
    {
        $this->db = DB::getInstance();
        $this->identityMap = new IdentityMap();
    }

    public function __destruct()
    {
        unset($this->identityMap, $this->db);
    }
}