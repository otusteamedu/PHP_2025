<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Database\Mysql;

use Zibrov\OtusPhp2025\Database\AbstractConfig;

class Config extends AbstractConfig
{

    public const FILE_NAME = '/upload/config/config_db_mysql.php';
    public const NAME_DATABASE_MANAGEMENT_SYSTEM = 'mysql';
    public const FILE_NAME_CREATE_TABLE = '/upload/db/mysql/create/ddl.sql';
    public const FILE_NAME_DELETE_TABLE = '/upload/db/mysql/delete/ddl.sql';

    public function __construct()
    {
        $this->setConfig(self::FILE_NAME);
    }
}
