<?php declare(strict_types=1);

namespace Sofyco\Spider\Parser\Result;

use Sofyco\Spider\Parser\Builder\NodeInterface;
use Symfony\Component\DomCrawler\Crawler;

final class XmlResult implements ResultInterface
{
    public function getResult(Crawler $crawler, NodeInterface $node): iterable
    {
        $elements = $crawler->filterXPath($node->getSelector());

        if (0 === $elements->count()) {
            return;
        }

        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            $value = \trim($element->textContent);

            if (false === empty($value)) {
                yield $value;
            }
        }
    }
}
