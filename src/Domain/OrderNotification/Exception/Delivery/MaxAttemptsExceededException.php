<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Exception\Delivery;

use App\Domain\OrderNotification\Exception\TerminalFailure;

class MaxAttemptsExceededException extends \LogicException implements TerminalFailure {}
