<?php declare(strict_types=1);

namespace Sofyco\Spider\Crawler;

use Sofyco\Spider\Context;
use Sofyco\Spider\ContextInterface;
use Sofyco\Spider\Parser\Builder\Node;
use Sofyco\Spider\Parser\Builder\NodeInterface;
use Sofyco\Spider\Parser\ParserInterface;
use Sofyco\Spider\Scraper\ScraperInterface;

final readonly class Crawler implements CrawlerInterface
{
    private NodeInterface $node;

    public function __construct(private ScraperInterface $scraper, private ParserInterface $parser)
    {
        $this->node = new Node(type: Node\Type::ATTRIBUTE, selector: 'a', attribute: 'href');
    }

    /**
     * @return ContextInterface[]
     */
    public function getResult(ContextInterface $context): iterable
    {
        $cache = [$context->getUrl()];

        yield from $this->getCachedResult($context, $cache);
    }

    public function getCachedResult(ContextInterface $context, array &$cache = []): iterable
    {
        $content = $this->scraper->getResult($context);

        foreach ($this->parser->getResult($content, $this->node) as $link) {
            if (null === $url = $this->prepareUrl($link, $context)) {
                continue;
            }

            if (\in_array($url, $cache)) {
                continue;
            }

            $cache[] = $url;
            $childContext = new Context(url: $url);

            yield $childContext;
            yield from $this->getCachedResult($childContext, $cache);
        }
    }

    private function prepareUrl(string $origin, ContextInterface $context): ?string
    {
        $child = (array) \parse_url($origin);
        $parent = (array) \parse_url($context->getUrl());

        $url = $child['scheme'] ?? $parent['scheme'] ?? 'https';
        $url .= '://';
        $url .= $child['host'] ?? $parent['host'] ?? throw new \InvalidArgumentException($origin);

        if (isset($child['path'])) {
            $url .= $child['path'];
        }

        if (isset($child['query'])) {
            $url .= '?' . $child['query'];
        }

        if (($child['host'] ?? '') !== ($parent['host'] ?? '')) {
            return null;
        }

        return $url;
    }
}
