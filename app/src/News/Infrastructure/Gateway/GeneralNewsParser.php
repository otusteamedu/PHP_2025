<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Gateway;

use App\News\Application\GateWay\NewsParserInterface;
use App\News\Application\GateWay\NewsParserRequest;
use App\News\Application\GateWay\NewsParserResponse;

class GeneralNewsParser implements NewsParserInterface
{
    public function getTitle(NewsParserRequest $request): NewsParserResponse
    {
        $dom = new \DOMDocument();
        $dom->formatOutput = true;
        $content = file_get_contents($request->url, false, null);
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', $this->getEncoding($content));
        @$dom->loadHTML($content);
        $title = $dom->getElementsByTagName('title')->item(0)->textContent;

        return new NewsParserResponse($title);
    }

    private function getEncoding(string $content): string
    {// todo разобраться почему с определением кодировки беда
        $encoding = mb_detect_encoding($content);

        return match ($encoding) {
            'ASCII' => 'Windows-1251',
            default => 'UTF-8',
        };
    }
}
