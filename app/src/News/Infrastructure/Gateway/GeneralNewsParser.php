<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Gateway;

use App\News\Application\GateWay\NewsParserInterface;
use DOMDocument;

class GeneralNewsParser implements NewsParserInterface
{
    public function getTitle(string $url): string
    {
        $dom = new DOMDocument();
        $dom->formatOutput = true;
        $content = file_get_contents($url, false, null);
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', $this->getEncoding($content));
        @$dom->loadHTML($content);

        return $dom->getElementsByTagName('title')->item(0)->textContent;
    }

    private function getEncoding(string $content): string
    {//todo разобраться почему с определением кодировки беда
        $encoding = mb_detect_encoding($content);
        return match ($encoding) {
            'ASCII' => 'Windows-1251',
            default => 'UTF-8',
        };
    }

}
