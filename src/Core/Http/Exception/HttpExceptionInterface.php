<?php

declare(strict_types=1);

namespace App\Core\Http\Exception;

interface HttpExceptionInterface
{
    public function getHttpCode(): int;
}
