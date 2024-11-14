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

    public function getResult(ContextInterface $context, NodeInterface $node): \Generator
    {
        $links = [$context->getUrl()];

        yield from $this->getRecursiveResult(context: $context, node: $node, links: $links);

        unset($links);
    }

    /**
     * @param string[] $links
     *
     * @return \Generator<ContextInterface>
     */
    private function getRecursiveResult(ContextInterface $context, NodeInterface $node, array &$links = []): \Generator
    {
        $content = $this->scraper->getResult(context: $context);

        foreach ($this->parser->getResult(content: $content, node: $node) as $link) {
            if (null === $url = $this->prepareUrl(link: $link, context: $context)) {
                continue;
            }

            if (\in_array($url, $links)) {
                continue;
            }

            $links[] = $url;
            $childContext = new Context(url: $url);

            yield $childContext;
            yield from $this->getRecursiveResult(context: $childContext, node: $node, links: $links);
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
        $url .= $child['host'] ?? $parent['host'] ?? throw new \InvalidArgumentException(message: $link);

        if (isset($child['path'])) {
            $url .= $child['path'];
        }

        if (isset($child['query'])) {
            $url .= '?' . $child['query'];
        }

        return $url;
    }
}
