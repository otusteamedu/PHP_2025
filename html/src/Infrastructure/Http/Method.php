<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Http;

enum Method: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
}
