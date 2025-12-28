<?php

declare(strict_types=1);

use App\Console\Command\ESBulkCommand;
use App\Console\Command\ESDeleteIndexCommand;
use App\Console\Command\ESSearchCommand;
use App\Console\Command\ESPingCommand;
use App\Console\Command\HelloCommand;

return [
    new HelloCommand(),
    new ESPingCommand(),
    new ESBulkCommand(),
    new ESDeleteIndexCommand(),
    new ESSearchCommand(),
];
