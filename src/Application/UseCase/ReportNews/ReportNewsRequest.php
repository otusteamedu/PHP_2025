<?php declare(strict_types=1);

namespace App\Application\UseCase\ReportNews;

readonly class ReportNewsRequest
{
    public function __construct(
        public array $ids,
    )
    {
    }
}
