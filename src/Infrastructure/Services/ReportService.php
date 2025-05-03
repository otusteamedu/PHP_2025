<?php declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Application\DTO\Report\RequestReportDTO;
use App\Application\DTO\Report\ResponseReportDTO;
use App\Application\Port\ReportServiceInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Domain\Repository\NewsRepositoryInterface;

final class ReportService implements ReportServiceInterface
{
    public function __construct(
        private KernelInterface  $kernel,
        private NewsRepositoryInterface $newsRepository
    )
    {
    }

    public function generateHtmlReport(RequestReportDTO $requestReportDTO):ResponseReportDTO
    {
        $arNews = $this->newsRepository->getByIds($requestReportDTO->arNewsIds);

        if (empty($arNews)) {
            throw new \RuntimeException('News not found');
        }

        $fileName = 'report_of_ids';
        $htmlReport = '<ul>';
        foreach ($arNews as $news) {
            $url = $news->getUrl();
            $title = $news->getTitle();
            $fileName .= '_' . $news->getId();
            $htmlReport .= '<li><a href="' . $url . '">' . $title . '</a><li>';
        }
        $htmlReport .= '</ul>';

        $filePath = $this->dumpReport($htmlReport, $fileName);
        return new ResponseReportDTO($filePath);
    }

    public function dumpReport(string $htmlReport, string $fileName):string
    {
        $filePath = $this->kernel->getProjectDir() . '/public/uploads/' . $fileName . '.html';
        $filesystem = new Filesystem();
        $filesystem->dumpFile($filePath, $htmlReport);
        return $filePath;
    }
}
