<?php declare(strict_types=1);

namespace Sofyco\Spider\Parser;

use Sofyco\Spider\Parser\Builder\Node\Type;
use Sofyco\Spider\Parser\Builder\NodeInterface;
use Sofyco\Spider\Parser\Result\ResultInterface;
use Symfony\Component\DomCrawler\Crawler;

final class Parser implements ParserInterface
{
    /**
     * @var \WeakMap<Type, Result\ResultInterface>
     */
    private \WeakMap $map;

    public function __construct()
    {
        $this->map = new \WeakMap();
        $this->map[Type::XML] = new Result\XmlResult();
        $this->map[Type::TEXT] = new Result\TextResult();
        $this->map[Type::HTML] = new Result\HtmlResult();
        $this->map[Type::ATTRIBUTE] = new Result\AttributeResult();
        $this->map[Type::LARGEST_NESTED_CONTENT] = new Result\LargestNestedContentResult();
    }

    public function getResult(string $content, NodeInterface $node): \Generator
    {
        $typeResult = $this->map[$node->getType()] ?? null;

        if (!$typeResult instanceof ResultInterface) {
            throw new Exception\UnexpectedTypeException($node->getType());
        }

        yield from $typeResult->getResult(crawler: new Crawler(node: $content), node: $node);
    }
}
