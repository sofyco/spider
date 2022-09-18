<?php declare(strict_types=1);

namespace Sofyco\Spider\Parser;

use Sofyco\Spider\Parser\Builder\NodeInterface;

/**
 * Parser processes or analyses given data to make of them proper data structures.
 */
interface ParserInterface
{
    /**
     * @param string        $response
     * @param NodeInterface $node
     *
     * @return string[]
     */
    public function getResult(string $response, NodeInterface $node): iterable;
}
