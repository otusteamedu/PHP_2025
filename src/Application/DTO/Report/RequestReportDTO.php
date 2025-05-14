<?php declare(strict_types=1);

namespace App\Application\DTO\Report;

final class RequestReportDTO
{
    public readonly array $arNewsIds;

    public function __construct(array $arNewsIds)
    {
        $this->arNewsIds = $arNewsIds;
    }
}