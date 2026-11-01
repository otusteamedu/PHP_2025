<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Exception;

/**
 * Повторная попытка бессмысленна - задача завершается ошибкой.
 */
interface TerminalFailure {}
