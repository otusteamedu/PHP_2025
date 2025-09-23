<?php
declare(strict_types=1);

namespace App\Application\Http;

enum RequestMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case DELETE = 'DELETE';
}
