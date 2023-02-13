<?php declare(strict_types=1);

namespace Sofyco\Spider\Scraper;

use Sofyco\Spider\ContextInterface;
use Sofyco\Spider\Scraper\Enum\HttpMethod;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Scraper implements ScraperInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function getResult(ContextInterface $context, HttpMethod $httpMethod = HttpMethod::GET): string
    {
        return $this->httpClient->request($httpMethod->value, $context->getUrl())->getContent();
    }
}
