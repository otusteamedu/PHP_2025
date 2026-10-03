<?php

declare(strict_types=1);

namespace App\Domain\BankReport\ValueObject;

enum ReportType: string
{
    case Summary = 'summary';
    case Detailed = 'detailed';
    case WithCommissions = 'with_commissions';

    public function getLabel(): string
    {
        return match ($this) {
            self::Summary => 'Краткий',
            self::Detailed => 'Подробный',
            self::WithCommissions => 'С комиссиями',
        };
    }

    public function getLowercaseLabel(): string
    {
        return mb_lcfirst($this->getLabel());
    }
}
