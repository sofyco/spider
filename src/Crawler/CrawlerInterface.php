<?php declare(strict_types=1);

namespace Sofyco\Spider\Crawler;

use Sofyco\Spider\ContextInterface;

/**
 * Crawler visits web pages and accumulates the links (urls) of the pages.
 */
interface CrawlerInterface
{
    /**
     * @return ContextInterface[]
     */
    public function getResult(ContextInterface $context): iterable;
}
