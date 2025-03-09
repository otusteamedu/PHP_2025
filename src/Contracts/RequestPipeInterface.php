<?php

namespace App\Contracts;

use App\Http\Request;

interface RequestPipeInterface
{
    /**
     * @param Request $request
     * @param $next
     * @return mixed
     */
    public function validate(Request $request, $next): mixed;
}