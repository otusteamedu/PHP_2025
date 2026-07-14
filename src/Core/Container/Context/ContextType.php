<?php

declare(strict_types=1);

namespace App\Core\Container\Context;

enum ContextType: string
{
    case CLI = 'cli';
    case HTTP_WEB = 'web';
    case HTTP_API = 'api';
}
