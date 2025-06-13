<?php

namespace App\Exception;

interface IApplicationException
{
    public function getHttpCode(): int;
}