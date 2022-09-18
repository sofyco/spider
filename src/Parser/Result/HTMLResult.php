<?php declare(strict_types=1);

namespace Sofyco\Spider\Parser\Result;

use Sofyco\Spider\Parser\Builder\NodeInterface;
use Symfony\Component\DomCrawler\Crawler;

final class HTMLResult implements ResultInterface
{
    public function getResult(Crawler $crawler, NodeInterface $node): iterable
    {
        $elements = $crawler->filter($node->getSelector());

        if (0 === $elements->count()) {
            return;
        }

        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            if (null !== $element->ownerDocument) {
                $value = \trim((string) $element->ownerDocument->saveHTML($element));

                if (false === empty($value)) {
                    yield $value;
                }
            }
        }
    }
}
