<?php declare(strict_types=1);

namespace Sofyco\Spider\Parser\Result;

use Sofyco\Spider\Parser\Builder\NodeInterface;
use Symfony\Component\DomCrawler\Crawler;

interface ResultInterface
{
    /**
     * @param Crawler $crawler
     * @param NodeInterface $node
     *
     * @return \Generator<string>
     */
    public function getResult(Crawler $crawler, NodeInterface $node): \Generator;
}
