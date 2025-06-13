<?php

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;

interface Controller
{
    public function __invoke(Request $request): Response;
}