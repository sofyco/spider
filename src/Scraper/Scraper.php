<?php declare(strict_types=1);

namespace Sofyco\Spider\Scraper;

use Sofyco\Spider\ContextInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Scraper implements ScraperInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function getResult(ContextInterface $context): string
    {
        return $this->httpClient->request(method: 'GET', url: $context->getUrl())->getContent();
    }
}
