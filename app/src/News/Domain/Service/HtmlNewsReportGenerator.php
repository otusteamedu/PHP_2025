<?php

declare(strict_types=1);

namespace App\News\Domain\Service;

use App\News\Application\Service\NewsReportInterface;
use App\News\Domain\Entity\News;
use App\News\Domain\Repository\NewsFilter;
use App\News\Domain\Repository\NewsRepositoryInterface;
use App\Shared\Domain\Service\FileHelper;
use Symfony\Component\Uid\Uuid;

class HtmlNewsReportGenerator implements NewsReportInterface
{
    private string $template = '<ul>{{CONTENT}}</ul>';

    public function __construct(
        private readonly NewsRepositoryInterface $newsRepository,
        private readonly FileHelper              $reportFileHelper,
    ) {
    }

    public function generate(string ...$newsId): string
    {
        $filter = new NewsFilter();
        $filter->setNewsIds(...$newsId);
        $news = $this->newsRepository->findByFilter($filter);
        if (!$news->total) {
            throw new \Exception('News not found');
        }
        $fileName = Uuid::v4()->toRfc4122() . '.html';
        $content = $this->parseReportContent(...$news->items);
        $this->reportFileHelper->save($content, $fileName);

        return $fileName;
    }

    private function parseReportContent(News ...$news): string
    {
        $content = '';
        foreach ($news as $newsItem) {
            $content .= sprintf('<li><a href="%s">%s</a><li>', $newsItem->getLink(), $newsItem->getTitle());
        }
        return str_replace('{{CONTENT}}', $content, $this->template);
    }
}
