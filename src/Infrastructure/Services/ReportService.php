<?php declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Application\Assembler\NewsAssembler;
use App\Domain\Repository\NewsRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

final class ReportService
{
    public function __construct(
        private KernelInterface  $kernel,
        private NewsAssembler  $newsAssembler,
        private NewsRepository $newsRepository
    )
    {
    }

    public function generateHtmlReport(array $arNewsIds):array
    {
        $arNews = $this->newsRepository->getByIds($arNewsIds);

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

        return [
            'message' => 'Order successfully generated',
            'generated_order' => $filePath,
        ];

    }
    private function dumpReport(string $htmlReport, string $fileName):string
    {
        $filePath = $this->kernel->getProjectDir() . '/public/uploads/' . $fileName . '.html';
        $filesystem = new Filesystem();
        $filesystem->dumpFile($filePath, $htmlReport);
        return $filePath;
    }
}
