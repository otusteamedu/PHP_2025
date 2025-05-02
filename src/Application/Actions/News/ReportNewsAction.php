<?php

namespace App\Application\Actions\News;

use App\Application\Actions\News\NewsAction;
use App\Application\Gateway\ReportGatewayInterface;
use App\Application\Gateway\ReportGatewayRequest;
use App\Application\UseCase\report\ReportNewsRequest;
use App\Application\UseCase\report\ReportNewsUseCase;
use App\Domain\Repository\NewsRepositoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class ReportNewsAction extends NewsAction
{

    protected ReportNewsUseCase $reportUseCase;

    public function __construct(
        LoggerInterface $logger,
        NewsRepositoryInterface $newsRepository,
        ReportNewsUseCase $reportUseCase,
    )
    {
        parent::__construct($logger, $newsRepository);
        $this->reportUseCase = $reportUseCase;
    }

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $newsIDs = array_filter(
            array_map(
                function ($id) {
                    return (int)$id;
                },
                explode(',',$this->getFormData()['ids']??'')
            )
        );

        $news = $this->newsRepository->findByIds($newsIDs);
        $report = ($this->reportUseCase)(new ReportNewsRequest($news));

        $this->logger->info("Report was created with IDs [".implode(', ',$newsIDs)."] to path: ".$report->reportPath);

        return $this->respondWithData($report->reportPath);
    }
}