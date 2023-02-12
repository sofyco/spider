<?php declare(strict_types=1);

namespace Sofyco\Spider\Crawler;

use Sofyco\Spider\ContextInterface;
use Sofyco\Spider\Parser\Builder\NodeInterface;

/**
 * Crawler visits web pages and accumulates the links (urls) of the pages.
 */
interface CrawlerInterface
{
    /**
     * @return ContextInterface[]
     */
    public function getResult(ContextInterface $context, NodeInterface $node): iterable;
}
