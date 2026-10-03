<?php

declare(strict_types=1);

namespace App\Controller\Http\Web\BankReport\Rule;

use App\Domain\BankReport\ValueObject\ReportType;

class ReportTypeRule implements RuleInterface
{
    public function validate(array $data, array &$errors): void
    {
        $reportType = $data['report_type'] ?? '';

        if (ReportType::tryFrom($reportType) === null) {
            $errors['report_type'][] = 'Некорректный тип выписки.';
        }
    }
}
