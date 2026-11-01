<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Exception;

/**
 * Ошибка может пройти при повторной попытке.
 */
interface RetryableFailure {}
