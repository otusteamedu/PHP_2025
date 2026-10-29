<?php

declare(strict_types=1);

namespace App\Core\Http\Message;

enum HttpStatus: int
{
    case Ok = 200;
    case Created = 201;
    case Accepted = 202;
    case BadRequest = 400;
    case NotFound = 404;
    case UnsupportedMediaType = 415;
    case UnprocessableContent = 422;
    case InternalServerError = 500;
    case ServiceTemporarilyUnavailable = 503;
}
