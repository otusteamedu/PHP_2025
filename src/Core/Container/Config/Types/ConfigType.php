<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Types;

enum ConfigType: string
{
    case DOT_ENV = 'config.dotenv';
    case CONSOLE = 'config.console';
    case SERVICE = 'config.service';
    case MODULE = 'config.module';
    case MODULE_AGGREGATOR = 'config.module_aggregator';
}
