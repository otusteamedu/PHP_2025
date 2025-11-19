<?php

declare(strict_types=1);

namespace App\Application\ProcessTaskMessage;

use Ramsey\Uuid\Uuid;

class ProcessTaskMessageQuery
{
    public function __construct(
        public string $id,
    ){

    }

}
