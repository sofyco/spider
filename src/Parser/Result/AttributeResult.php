<?php declare(strict_types=1);

namespace Sofyco\Spider\Parser\Result;

use Sofyco\Spider\Parser\Builder\NodeInterface;
use Symfony\Component\DomCrawler\Crawler;

final class AttributeResult implements ResultInterface
{
    public function getResult(Crawler $crawler, NodeInterface $node): \Generator
    {
        $elements = $crawler->filter(selector: $node->getSelector());

        if (0 === $elements->count()) {
            return;
        }

        if (null === $node->getAttribute()) {
            return;
        }

        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            $value = \trim($element->getAttribute(qualifiedName: $node->getAttribute()));

            if (false === empty($value)) {
                yield $value;
            }
        }
    }
}
