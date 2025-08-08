<?php

declare(strict_types=1);

namespace App\Application\ReportNews;

use App\Domain\Repository\NewsRepositoryInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class ReportNewsHandler
{
    public function __construct(
        private readonly NewsRepositoryInterface $repository,
        private readonly KernelInterface         $kernel
    )
    {
    }

    public function __invoke(ReportNewsQuery $query): ReportNewsOutput
    {
        $ids = $query->ids;
        $news = $this->repository->findBy(['id' => $ids]);

        $newsResult = [];
        foreach ($news as $new) {
            $newsResult[] = [
                'title' => $new->getTitle(),
                'url' => $new->getUrl(),
            ];
        }

        $this->createHtmlFile($newsResult);

        $path = $this->kernel->getProjectDir() . '/public/news.html';

        $html = file_get_contents($path);

        return new ReportNewsOutput($html);
    }

    private function createHtmlFile(array $newsResult): void
    {
        $html = [];
        foreach ($newsResult as $item) {
            $title = $item['title'];
            $url = $item['url'];
            $html[] = "<li><a href=\"$url\">$title</a></li>\n";
        }

        file_put_contents('news.html', $html);
    }
}
