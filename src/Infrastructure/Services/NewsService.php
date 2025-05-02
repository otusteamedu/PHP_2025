<?php declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Application\Assembler\NewsAssembler;
use App\Application\DTO\CreateNewsDTO;
use App\Application\DTO\NewsDTO;
use App\Application\DTO\ResponseNewsDTO;
use App\Domain\Repository\NewsRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Application\Port\NewsServiceInterface;

final class NewsService implements NewsServiceInterface
{

    //TODO разбить на сервис новостей и сервис генерации отчета
    public function __construct(
        private KernelInterface  $kernel,
        private NewsAssembler  $newsAssembler,
        private NewsRepository $newsRepository
    )
    {
    }


    public function createNews(CreateNewsDTO $createNewsDTO): ResponseNewsDTO
    {
        $url = $createNewsDTO->url;
        $arNewsTitles = $this->getHtmlByUrl($url, 'title');
        if (is_array($arNewsTitles) && !empty($arNewsTitles)) {
            $mainTitle = reset($arNewsTitles);
            $createDate = new \DateTime('now', new \DateTimeZone('Europe/Moscow'));

            $newsDTO = new NewsDTO($mainTitle, $url, $createDate);
            $newsEntity = $this->newsAssembler->toEntity($newsDTO);

            $this->newsRepository->save($newsEntity);

            //TODO принимать и возвращать DTO (на входе и на выходе) +
            return new ResponseNewsDTO(
                $newsEntity->getId(),
                $newsEntity->getTitle(),
                $newsEntity->getUrl()
            );

        } else {
            throw new \RuntimeException('This new hasn`t a title');
        }
    }

    public function getNews(): array
    {
        //TODO массив DTO новостей!
        $arNewsList = $this->newsRepository->getList();
        return $arNewsList;
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
