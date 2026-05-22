<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

/**
 * Исключение для отсутствующего запроса на обработку
 */
final class RequestNotFoundException extends RuntimeException
{
}
