<?php
declare(strict_types=1);

namespace App\Database;

final readonly class Config
{
    public string $hostname;
    public int $port;
    public string $database;
    public string $username;
    public string $password;
    public bool $ssl;

    public function __construct()
    {
        $this->hostname = getenv('DB_HOST');
        $this->port = (int) getenv('DB_PORT');
        $this->database = getenv('DB_DATABASE');
        $this->username = getenv('DB_USERNAME');
        $this->password = getenv('DB_PASSWORD');
        $this->ssl = (bool) getenv('DB_SSL');
    }
}
