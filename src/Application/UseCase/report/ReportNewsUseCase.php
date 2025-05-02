<?php

namespace App\Application\UseCase\report;

use App\Application\Gateway\InternetGatewayInterface;
use App\Application\Gateway\InternetGatewayRequest;
use App\Application\Gateway\ReportGatewayInterface;
use App\Application\Gateway\ReportGatewayRequest;
use App\Application\UseCase\AddUrl\AddUrlRequest;
use App\Application\UseCase\AddUrl\AddUrlResponse;
use App\Domain\Entity\News;
use App\Domain\Factory\NewsFactoryInterface;
use App\Domain\Repository\NewsRepositoryInterface;
use App\Infrastructure\Gateway\ReportGateway;

class ReportNewsUseCase
{
    public function __construct(
        private readonly ReportGatewayInterface $reportGateway
    )
    {
    }
    public function __invoke(ReportNewsRequest $reportNewsList): ReportNewsResponse
    {
        // Получить путь
        $reportRequest = new ReportGatewayRequest(
            $this->convertToHTMLLinks($reportNewsList->newsList)
        );
        $reportResponse = $this->reportGateway->saveReport($reportRequest);

        // Вернуть результат
        return new ReportNewsResponse(
            $reportResponse->reportPath
        );
    }

    private function convertToHTMLLinks(iterable $newsList): string
    {
        $linksList = [];
        foreach ($newsList as $news){
            /**
             * @var News $news
             */
            $linksList[] = '<li><a href="'.$news->getUrl()->getValue().'">'.$news->getTitle()->getValue().'</a><li>';
        }
        return '<ul>'.implode('', $linksList).'</ul>';
    }
}