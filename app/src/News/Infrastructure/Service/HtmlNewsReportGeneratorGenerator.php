<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Service;

use App\News\Application\DTO\NewsDTO;
use App\News\Application\Service\NewsReportGeneratorInterface;
use App\Shared\Infrastructure\Service\FileHelper;
use Symfony\Component\Uid\Uuid;

class HtmlNewsReportGeneratorGenerator implements NewsReportGeneratorInterface
{
    private string $template = '<ul>{{CONTENT}}</ul>';

    public function __construct(private readonly FileHelper $reportFileHelper)
    {
    }

    /**
     * @throws \Exception
     */
    public function generate(array $newsDTOs): string
    {
        $fileName = Uuid::v4()->toString().'.html';
        $content = $this->parseReportContent($newsDTOs);
        $this->reportFileHelper->save($content, $fileName);

        return $fileName;
    }

    private function parseReportContent(array $newsDTOs): string
    {
        $content = '';

        /** @var NewsDTO $dto */
        foreach ($newsDTOs as $dto) {
            $content .= sprintf('<li><a href="%s">%s</a></li>', $dto->link, $dto->title);
        }

        return str_replace('{{CONTENT}}', $content, $this->template);
    }
}
