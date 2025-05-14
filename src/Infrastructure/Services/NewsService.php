<?php declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Application\Port\NewsServiceInterface;

final class NewsService implements NewsServiceInterface
{
    public function getHtmlByUrl(string $url, string $tag = '')
    {
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
