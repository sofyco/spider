<?php declare(strict_types=1);

namespace Sofyco\Spider\Crawler;

use Sofyco\Spider\Context;
use Sofyco\Spider\ContextInterface;
use Sofyco\Spider\Parser\Builder\NodeInterface;
use Sofyco\Spider\Parser\ParserInterface;
use Sofyco\Spider\Scraper\ScraperInterface;

final class Crawler implements CrawlerInterface
{
    private array $urls = [];

    public function __construct(private readonly ScraperInterface $scraper, private readonly ParserInterface $parser)
    {
    }

    public function getResult(ContextInterface $context, NodeInterface $node): \Generator
    {
        $this->addCachedUrl(url: $context->getUrl());

        yield from $this->getRecursiveResult(context: $context, node: $node);
    }

    /**
     * @return \Generator<ContextInterface>
     */
    private function getRecursiveResult(ContextInterface $context, NodeInterface $node): \Generator
    {
        $childContext = [];
        $content = $this->scraper->getResult(context: $context);

        foreach ($this->parser->getResult(content: $content, node: $node) as $url) {
            if (null === $url = $this->prepareUrl(url: $url, context: $context)) {
                continue;
            }

            if ($this->hasCachedUrl(url: $url)) {
                continue;
            }

            $this->addCachedUrl(url: $url);
            $childContext[] = new Context(url: $url);
        }

        yield from $childContext;

        foreach ($childContext as $child) {
            yield from $this->getRecursiveResult(context: $child, node: $node);
        }
    }

    private function prepareUrl(string $url, ContextInterface $context): ?string
    {
        $child = (array) parse_url($url);
        $parent = (array) parse_url($context->getUrl());

        if (false === empty($child['host']) && $child['host'] !== ($parent['host'] ?? '')) {
            return null;
        }

        $result = $child['scheme'] ?? $parent['scheme'] ?? 'https';
        $result .= '://';
        $result .= $child['host'] ?? $parent['host'] ?? throw new \InvalidArgumentException(message: $url);

        if (isset($child['path'])) {
            $result .= $child['path'];
        }

        if (isset($child['query'])) {
            $result .= '?' . $child['query'];
        } else {
            $result = rtrim($result, '/');
        }

        return $result;
    }

    private function addCachedUrl(string $url): void
    {
        $this->urls[] = md5($url);
    }

    private function hasCachedUrl(string $url): bool
    {
        return in_array(md5($url), $this->urls);
    }
}
