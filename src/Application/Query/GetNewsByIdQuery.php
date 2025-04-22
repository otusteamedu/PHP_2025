<?php

namespace App\Application\Query;

class GetNewsByIdQuery
{
    public int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}