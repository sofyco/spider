<?php declare(strict_types=1);

namespace Sofyco\Spider\Crawler;

use Sofyco\Spider\Context;
use Sofyco\Spider\ContextInterface;
use Sofyco\Spider\Parser\Builder\NodeInterface;
use Sofyco\Spider\Parser\ParserInterface;
use Sofyco\Spider\Scraper\ScraperInterface;

final readonly class Crawler implements CrawlerInterface
{
    public function __construct(private ScraperInterface $scraper, private ParserInterface $parser)
    {
    }

    /**
     * @return ContextInterface[]
     */
    public function getResult(ContextInterface $context, NodeInterface $node): iterable
    {
        $links = [$context->getUrl()];

        yield from $this->getRecursiveResult($context, $node, $links);

        unset($links);
    }

    private function getRecursiveResult(ContextInterface $context, NodeInterface $node, array &$links = []): iterable
    {
        $content = $this->scraper->getResult($context);

        foreach ($this->parser->getResult($content, $node) as $link) {
            if (null === $url = $this->prepareUrl($link, $context)) {
                continue;
            }

            if (\in_array($url, $links)) {
                continue;
            }

            $links[] = $url;
            $childContext = new Context(url: $url);

            yield $childContext;
            yield from $this->getRecursiveResult($childContext, $node, $links);
        }
    }

    private function prepareUrl(string $link, ContextInterface $context): ?string
    {
        $child = (array) \parse_url($link);
        $parent = (array) \parse_url($context->getUrl());

        if (false === empty($child['host']) && $child['host'] !== ($parent['host'] ?? '')) {
            return null;
        }

        $url = $child['scheme'] ?? $parent['scheme'] ?? 'https';
        $url .= '://';
        $url .= $child['host'] ?? $parent['host'] ?? throw new \InvalidArgumentException($link);

        if (isset($child['path'])) {
            $url .= $child['path'];
        }

        if (isset($child['query'])) {
            $url .= '?' . $child['query'];
        }

        return $url;
    }
}
