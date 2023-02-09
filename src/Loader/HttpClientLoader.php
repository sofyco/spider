<?php declare(strict_types=1);

namespace Sofyco\Spider\Loader;

use Sofyco\Spider\ContextInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class HttpClientLoader implements LoaderInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function getContent(ContextInterface $context): string
    {
        return $this->httpClient->request('GET', $context->getUrl())->getContent();
    }
}
