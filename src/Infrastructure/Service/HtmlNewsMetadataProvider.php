<?php declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Application\Service\NewsMetadataProviderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HtmlNewsMetadataProvider implements NewsMetadataProviderInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function fetchTitle(string $url): string
    {
        try {
            $response = $this->httpClient->request('GET', $url);
            $html = $response->getContent();
            preg_match('/<title>(.*?)<\/title>/si', $html, $matches);

            return $matches[1] ?? '';
        } catch (\Exception $e) {
            throw new \RuntimeException('Cannot fetch news title: ' . $e->getMessage());
        }
    }
}
