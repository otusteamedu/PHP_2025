<?php

declare(strict_types=1);

namespace App\Controller\Http\Web\BankReport\Validator;

use App\Controller\Http\Web\BankReport\DataObject\BankReportFormDto;
use App\Controller\Http\Web\BankReport\DataObject\ValidationResult;
use App\Controller\Http\Web\BankReport\Rule\ClientNameRule;
use App\Controller\Http\Web\BankReport\Rule\DateFormatRule;
use App\Controller\Http\Web\BankReport\Rule\DateRangeAndFutureRule;
use App\Controller\Http\Web\BankReport\Rule\EmailRule;
use App\Controller\Http\Web\BankReport\Rule\ReportTypeRule;
use App\Controller\Http\Web\BankReport\Rule\RuleInterface;

class BankReportFormValidator
{
    /** @var RuleInterface[] */
    private array $rules;

    public function __construct(
        ClientNameRule $clientNameRule,
        DateFormatRule $dateFormatRule,
        DateRangeAndFutureRule $dateRangeAndFutureRule,
        ReportTypeRule $reportTypeRule,
        EmailRule $emailRule,
    ) {
        $this->rules = [
            $clientNameRule,
            $dateFormatRule,
            $dateRangeAndFutureRule,
            $reportTypeRule,
            $emailRule,
        ];
    }

    public function validate(array $rawData): ValidationResult
    {
        $errors = [];

        foreach ($this->rules as $rule) {
            $rule->validate($rawData, $errors);
        }

        if (!empty($errors)) {
            return ValidationResult::withErrors($errors);
        }

        return ValidationResult::ok(
            new BankReportFormDto(
                clientName: trim($rawData['client_name']),
                dateFrom: $rawData['date_from'],
                dateTo: $rawData['date_to'],
                reportType: $rawData['report_type'],
                email: trim($rawData['email']),
            ),
        );
    }
}
