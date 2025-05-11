<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Database\Postgresql;

use Zibrov\OtusPhp2025\Database\AbstractConfig;

class Config extends AbstractConfig
{

    public const FILE_NAME = '/upload/config/config_db_postgresql.php';
    public const NAME_DATABASE_MANAGEMENT_SYSTEM = 'pgsql';
    public const FILE_NAME_CREATE_TABLE = '/upload/db/postgresql/create/ddl.sql';
    public const FILE_NAME_DELETE_TABLE = '/upload/db/postgresql/delete/ddl.sql';

    public function __construct()
    {
        $this->setConfig(self::FILE_NAME);
    }
}

