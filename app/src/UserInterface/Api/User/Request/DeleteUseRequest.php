<?php

declare(strict_types=1);

namespace App\UserInterface\Api\User\Request;

use App\UserInterface\Api\User\UserController;

class DeleteUseRequest
{
    public function __construct(
        public int $id,
    )
    {

    }

}