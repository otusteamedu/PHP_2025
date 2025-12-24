<?php declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Application\Service\ReportGenerator\ReportGeneratorInterface;
use App\Application\Service\ReportGenerator\ReportGeneratorRequest;
use App\Application\Service\ReportGenerator\ReportGeneratorResponse;

class HtmlReportGenerator implements ReportGeneratorInterface
{
    public function __construct(
        private string $reportsDir,
    )
    {
    }

    public function generate(ReportGeneratorRequest $request): ReportGeneratorResponse
    {
        $newsList = $request->newsList;

        $html = "<ul>\n";
        foreach ($newsList as $news) {
            $html .= sprintf(
                '<li><a href="%s">%s</a></li>' . "\n",
                htmlspecialchars($news->getUrl()->getValue()),
                htmlspecialchars($news->getTitle()->getValue())
            );
        }
        $html .= "</ul>";

        if (!is_dir($this->reportsDir)) {
            mkdir($this->reportsDir, 0777, true);
        }

        $filename = 'report_' . time() . '.html';
        $fullPath = $this->reportsDir . '/' . $filename;

        file_put_contents($fullPath, $html);

        return new ReportGeneratorResponse($fullPath);
    }
}
