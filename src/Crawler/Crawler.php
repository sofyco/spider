<?php declare(strict_types=1);

namespace Sofyco\Spider\Crawler;

use Sofyco\Spider\Context;
use Sofyco\Spider\ContextInterface;
use Sofyco\Spider\Parser\Builder\Node;
use Sofyco\Spider\Parser\Builder\NodeInterface;
use Sofyco\Spider\Parser\ParserInterface;
use Sofyco\Spider\Scraper\ScraperInterface;

final class Crawler implements CrawlerInterface
{
    public function __construct(private readonly ScraperInterface $scraper, private readonly ParserInterface $parser)
    {
    }

    /**
     * @param ContextInterface $context
     *
     * @return ContextInterface[]
     */
    public function getResult(ContextInterface $context): iterable
    {
        $node = new Node(type: Node\Type::ATTRIBUTE, selector: 'a', attribute: 'href');
        $result = [];

        $this->addLinks($result, $context, $node);

        yield from \array_values($result);
    }

    private function addLinks(array &$result, ContextInterface $context, NodeInterface $node): void
    {
        $content = $this->scraper->getResult($context);
        $links = $this->parser->getResult($content, $node);
        $parent = (array) \parse_url($context->getUrl());
        $scheme = $parent['scheme'] ?? '';
        $host = $parent['host'] ?? '';

        foreach ($links as $url) {
            $urlHost = \parse_url($url, \PHP_URL_HOST);

            if (null === $urlHost) {
                $url = $scheme . '://' . $host . (\str_starts_with($url, '/') ? '' : '/') . $url;
            } elseif ($urlHost !== $host) {
                continue;
            }

            if (isset($result[$url]) || $url === $context->getUrl()) {
                continue;
            }

            $result[$url] = new Context(url: $url, parent: $context);

            $this->addLinks($result, $result[$url], $node);
        }
    }
}
