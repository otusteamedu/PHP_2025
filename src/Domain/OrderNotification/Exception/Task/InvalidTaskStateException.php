<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Exception\Task;

use App\Domain\OrderNotification\Exception\TerminalFailure;

class InvalidTaskStateException extends \LogicException implements TerminalFailure {}
