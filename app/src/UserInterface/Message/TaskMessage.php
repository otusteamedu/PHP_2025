<?php

declare(strict_types=1);

namespace App\UserInterface\Message;

use Ramsey\Uuid\Uuid;

class TaskMessage
{
    public function __construct(
        public string $id,
    )
    {

    }

}
