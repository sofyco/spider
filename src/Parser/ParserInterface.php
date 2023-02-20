<?php declare(strict_types=1);

namespace Sofyco\Spider\Parser;

use Sofyco\Spider\Parser\Builder\NodeInterface;

/**
 * The parser processes or parses given data to make of them proper data structures.
 */
interface ParserInterface
{
    /**
     * @param string $content
     * @param NodeInterface $node
     *
     * @return \Generator<string>
     */
    public function getResult(string $content, NodeInterface $node): \Generator;
}
