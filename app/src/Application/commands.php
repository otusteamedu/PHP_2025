<?php
declare(strict_types=1);

use App\Commands\InitDatabaseCommand;
use App\Commands\UsersListCommand;

return [
    InitDatabaseCommand::class,
    UsersListCommand::class,
];
