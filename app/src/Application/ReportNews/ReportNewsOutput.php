<?php

declare(strict_types=1);

namespace App\Application\ReportNews;

class ReportNewsOutput
{
    public function __construct(
        public string $html,
    )
    {
    }
}
