<?php declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Application\Service\ReportGeneratorInterface;

class HtmlReportGenerator implements ReportGeneratorInterface
{
    public function generate(iterable $newsList): string
    {
        $html = "<ul>\n";
        foreach ($newsList as $news) {
            $html .= sprintf(
                '<li><a href="%s">%s</a></li>' . "\n",
                htmlspecialchars($news->getUrl()->getValue()),
                htmlspecialchars($news->getTitle()->getValue())
            );
        }
        $html .= "</ul>";

        return $html;
    }

    public function saveToFile(string $html, string $filename): void
    {
        file_put_contents($filename, $html);
    }
}
