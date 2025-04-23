<?php declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Assembler\NewsAssembler;
use App\Application\DTO\NewsDTO;
use App\Domain\Entity\News;
use App\Domain\Repository\NewsRepository;
use http\Exception\RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

final class NewsService
{

    public function __construct(
        private KernelInterface  $kernel,
        private NewsAssembler  $newsAssembler,
        private NewsRepository $newsRepository
    )
    {
    }

    public function getHtmlByUrl(string $url, string $tag = '')
    {
        //TODO проверить получен ли html
        $htmlFile = file_get_contents($url);
        $doc = new \DOMDocument();
        $doc->loadHTML('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $htmlFile, LIBXML_NOERROR);

        if ($tag != '') {
            $elements = $doc->getElementsByTagName($tag);
            if (!is_null($elements)) {
                $arTagValues = [];
                foreach ($elements as $element) {
                    $nodes = $element->childNodes;
                    foreach ($nodes as $node) {
                        $arTagValues[] = $node->nodeValue;
                    }
                }
            }
            return $arTagValues;
        } else {
            return $doc->saveHTML();
        }
    }

    public function createNews(string $url): array
    {
        $arNewsTitles = $this->getHtmlByUrl($url, 'title');
        if (is_array($arNewsTitles) && !empty($arNewsTitles)) {
            $mainTitle = reset($arNewsTitles);
            $createDate = new \DateTime('now', new \DateTimeZone('Europe/Moscow'));

            $newsDTO = new NewsDTO($mainTitle, $url, $createDate);
            $newsEntity = $this->newsAssembler->toEntity($newsDTO);

            $this->newsRepository->save($newsEntity);

            return [
                'id' => $newsEntity->getId(),
                'title' => $newsEntity->getTitle(),
                'url' => $newsEntity->getUrl(),
            ];

        } else {
            throw new \RuntimeException('This new hasn`t a title');
        }
    }

    public function getNews(): array
    {
        $arNewsList = $this->newsRepository->getList();
        return $arNewsList;
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
