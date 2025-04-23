<?php
declare(strict_types=1);


namespace App\News\Application\UseCase\GenerateNewsReport;

class GenerateNewsReportRequest
{
    public array $newsIds = [];

    public function __construct(string ...$newsId)
    {
        $this->newsIds = $newsId;
    }

}