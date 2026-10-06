<?php

declare(strict_types=1);

namespace App\Controller\Http\Web\BankReport;

use App\Controller\Http\Web\BankReport\DataObject\BankReportFormDto;
use App\Controller\Http\Web\BankReport\Validator\BankReportFormValidator;
use App\Core\Http\Controller\Base\AbstractController;
use App\Core\Http\Message\Request;
use App\Core\Http\Message\Response;
use App\Core\Http\View\View;
use App\Domain\BankReport\Exception\ReportRequestException;
use App\Domain\BankReport\ReportService;
use App\Domain\BankReport\ValueObject\ClientName;
use App\Domain\BankReport\ValueObject\DateRange;
use App\Domain\BankReport\ValueObject\Email;
use App\Domain\BankReport\ValueObject\ReportGenerationRequest;
use App\Domain\BankReport\ValueObject\ReportType;

class BankReportController extends AbstractController
{
    public function __construct(
        private readonly Request $request,
        private readonly View $view,
        private readonly BankReportFormValidator $validator,
        private readonly ReportService $reportService,
    ) {
    }

    public function requestReport(): Response
    {
        if ($this->request->getRequestMethod() !== 'POST') {
            return $this->handleGetRequest();
        }

        return $this->handlePostRequest();
    }

    protected function handleGetRequest(): Response
    {
        return $this->renderForm();
    }

    protected function handlePostRequest(): Response
    {
        $data = $this->request->getFormData();

        $result = $this->validator->validate($data);
        if (!$result->isValid()) {
            return $this->renderForm($data, $result->getErrors());
        }

        $dto = $result->getDto();
        $reportRequest = $this->buildReportRequest($dto);

        try {
            $this->reportService->requestAsync($reportRequest);
            return $this->renderSuccess();
        } catch (ReportRequestException $e) {
            return $this->renderForm($data, ['global' => [$e->getMessage()]]);
        }
    }

    private function renderForm(array $data = [], array $errors = []): Response
    {
        $defaults = DateRange::defaultRange()->toArray();

        return $this->render(
            $this->view,
            'bank_reports/bank_report_request.php',
            [
                'defaultFrom' => $defaults['dateFrom'],
                'defaultTo' => $defaults['dateTo'],
                'data' => $data,
                'errors' => $errors,
            ],
        );
    }

    private function renderSuccess(): Response
    {
        return $this->render($this->view, 'bank_reports/bank_report_accepted.php');
    }

    private function buildReportRequest(BankReportFormDto $dto): ReportGenerationRequest
    {
        return ReportGenerationRequest::create(
            clientName: new ClientName($dto->clientName),
            dateRange: new DateRange($dto->dateFrom, $dto->dateTo),
            reportType: ReportType::from($dto->reportType),
            email: new Email($dto->email),
        );
    }
}
