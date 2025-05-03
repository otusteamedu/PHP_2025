<?php declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Application\Factories\NewsFactory;
use App\Application\DTO\News\CreateNewsDTO;
use App\Application\DTO\News\NewsDTO;
use App\Application\DTO\News\ResponseNewsDTO;
use App\Application\Port\NewsServiceInterface;
use App\Domain\Repository\NewsRepositoryInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class NewsService implements NewsServiceInterface
{
    //TODO разбить на сервис новостей и сервис генерации отчета
    public function __construct(
        private KernelInterface  $kernel,
        private NewsFactory  $newsFactory,
        private NewsRepositoryInterface $newsRepository
    )
    {}

    public function createNews(CreateNewsDTO $createNewsDTO): ResponseNewsDTO
    {
        $url = $createNewsDTO->url;
        $arNewsTitles = $this->getHtmlByUrl($url, 'title');
        if (is_array($arNewsTitles) && !empty($arNewsTitles)) {
            $mainTitle = reset($arNewsTitles);
            $createDate = new \DateTime('now', new \DateTimeZone('Europe/Moscow'));

            $newsDTO = new NewsDTO($mainTitle, $url, $createDate);
            $newsEntity = $this->newsFactory->toEntity($newsDTO);

            $this->newsRepository->save($newsEntity);

            //TODO принимать и возвращать DTO (на входе и на выходе) +
            return new ResponseNewsDTO(
                $newsEntity->getId(),
                $newsEntity->getTitle(),
                $newsEntity->getUrl(),
                $createDate
            );

        } else {
            throw new \RuntimeException('This new hasn`t a title');
        }
    }

    public function getNews(): array
    {
        return $this->newsRepository->getList();
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
}
