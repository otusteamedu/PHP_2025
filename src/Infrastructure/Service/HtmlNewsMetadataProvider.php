<?php declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Application\Service\NewsMetadataProvider\NewsMetadataProviderInterface;
use App\Application\Service\NewsMetadataProvider\NewsMetadataProviderRequest;
use App\Application\Service\NewsMetadataProvider\NewsMetadataProviderResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HtmlNewsMetadataProvider implements NewsMetadataProviderInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function fetchTitle(NewsMetadataProviderRequest $request): NewsMetadataProviderResponse
    {
        try {
            $url = $request->url;
            $response = $this->httpClient->request('GET', $url);
            $html = $response->getContent();
            preg_match('/<title>(.*?)<\/title>/si', $html, $matches);

            return new NewsMetadataProviderResponse($matches[1] ?? '');
        } catch (\Exception $e) {
            throw new \RuntimeException('Cannot fetch news title: ' . $e->getMessage());
        }
    }
}
