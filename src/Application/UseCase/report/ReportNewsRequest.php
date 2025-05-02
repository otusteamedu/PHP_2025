<?php

namespace App\Application\UseCase\report;

class ReportNewsRequest
{
    public function __construct(
        public readonly iterable $newsList
    )
    {

    }
}