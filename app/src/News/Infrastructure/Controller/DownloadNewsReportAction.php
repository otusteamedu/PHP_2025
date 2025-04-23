<?php
declare(strict_types=1);


namespace App\News\Infrastructure\Controller;

use App\News\Application\UseCase\DownloadNewsReport\DownloadNewsReportRequest;
use App\News\Application\UseCase\DownloadNewsReport\DownloadNewsReportUseCase;
use League\Flysystem\FilesystemException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/news/report/download/{fileName}', name: 'app_api_news_report_download', methods: ['GET'])]
class DownloadNewsReportAction extends AbstractController
{
    public function __construct(
        private readonly DownloadNewsReportUseCase $downloadNewsReportUseCase,
    )
    {
    }

    /**
     * @throws FilesystemException
     */
    public function __invoke(string $fileName): StreamedResponse
    {
        $request = new DownloadNewsReportRequest($fileName);
        $result = ($this->downloadNewsReportUseCase)($request);
        $response = new StreamedResponse(function () use ($result) {
            $outputStream = fopen('php://output', 'w');
            stream_copy_to_stream($result->stream, $outputStream);
        });

        return $this->setHeaders($response, $fileName, $result->mimeType);
    }

    private function setHeaders(StreamedResponse $response, string $fileName, string $mimeType): StreamedResponse
    {
        $disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $fileName);
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', $mimeType);

        return $response;
    }

}