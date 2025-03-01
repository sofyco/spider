<?php declare(strict_types=1);

namespace Sofyco\Spider\Crawler;

use Sofyco\Spider\Context;
use Sofyco\Spider\ContextInterface;
use Sofyco\Spider\Parser\Builder\NodeInterface;
use Sofyco\Spider\Parser\ParserInterface;
use Sofyco\Spider\Scraper\ScraperInterface;

final class Crawler implements CrawlerInterface
{
    /**
     * @var string[]
     */
    private array $cache;

    /**
     * @var \SplQueue<ContextInterface>
     */
    private readonly \SplQueue $queue;

    public function __construct(private readonly ScraperInterface $scraper, private readonly ParserInterface $parser)
    {
        $this->cache = [];
        $this->queue = new \SplQueue();
    }

    public function getResult(ContextInterface $context, NodeInterface $node): \Generator
    {
        $this->addCachedUrl(url: $context->getUrl());
        $this->queue->enqueue($context);

        while (false === $this->queue->isEmpty()) {
            foreach ($this->getChildContext(context: $this->queue->dequeue(), node: $node) as $childContext) {
                $this->queue->enqueue($childContext);

                yield $childContext;
            }
        }
    }

    /**
     * @return \Generator<ContextInterface>
     */
    private function getChildContext(ContextInterface $context, NodeInterface $node): \Generator
    {
        $result = [];

        foreach ($this->parser->getResult(content: $this->scraper->getResult(context: $context), node: $node) as $url) {
            if (null === $url = $this->prepareUrl(url: $url, context: $context)) {
                continue;
            }

            if ($this->hasCachedUrl(url: $url)) {
                continue;
            }

            $this->addCachedUrl(url: $url);

            $result[] = new Context(url: $url);
        }

        yield from $result;
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
        $this->cache[] = $url;
    }

    private function hasCachedUrl(string $url): bool
    {
        return in_array($url, $this->cache);
    }
}
