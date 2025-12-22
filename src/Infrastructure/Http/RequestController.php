<?php
declare(strict_types=1);

namespace Dinargab\Homework19\Infrastructure\Http;

use Dinargab\Homework19\Application\Report\UseCase\GenerateReportRequest;
use Dinargab\Homework19\Application\Report\UseCase\GenerateReportUseCase;

class RequestController extends AbstractController
{

    public function __construct(
        private GenerateReportUseCase $generateReportUseCase,
    )
    {

    }
    public function __invoke($request)
    {
        $request = new GenerateReportRequest($_POST["dateFrom"], $_POST["dateTo"], $_POST["email"]);

        $response = ($this->generateReportUseCase)($request);

        $this->render("index", [
            "response" => $response,
        ]);
    }

    public function showForm()
    {
        $this->render("index", []);
    }
}