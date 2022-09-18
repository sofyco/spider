<?php declare(strict_types=1);

namespace Sofyco\Spider\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Sofyco\Spider\Parser\Builder\Node\Type;
use Sofyco\Spider\Parser\Builder\NodeInterface;
use Sofyco\Spider\Parser\Parser;

final class ParserTest extends TestCase
{
    /**
     * @dataProvider typeProvider
     */
    public function testTypeResult(NodeInterface $node, string $response, array $expected): void
    {
        $parser = new Parser();
        $result = $parser->getResult($response, $node);

        self::assertIsIterable($result);

        foreach ($result as $index => $value) {
            self::assertSame($expected[$index], $value);
        }
    }

    public function typeProvider(): iterable
    {
        $response = (string) \file_get_contents(__DIR__ . '/stub/index.html');

        $node = $this->createMock(NodeInterface::class);
        $node->expects($this->any())->method('getType')->willReturn(Type::TEXT);
        $node->expects($this->any())->method('getSelector')->willReturn('a');

        yield 'Text type' => [
            'node' => $node,
            'response' => $response,
            'expected' => [
                'Link #1',
                'Link #2',
                'Link #3',
            ],
        ];

        $node = $this->createMock(NodeInterface::class);
        $node->expects($this->any())->method('getType')->willReturn(Type::TEXT);
        $node->expects($this->any())->method('getSelector')->willReturn('strong');

        yield 'Text type of undefined element' => [
            'node' => $node,
            'response' => $response,
            'expected' => [],
        ];

        $node = $this->createMock(NodeInterface::class);
        $node->expects($this->any())->method('getType')->willReturn(Type::HTML);
        $node->expects($this->any())->method('getSelector')->willReturn('ul li a');

        yield 'HTML type' => [
            'node' => $node,
            'response' => $response,
            'expected' => [
                '<a href="https://localhost">Link #1</a>',
                '<a href="https://localhost">Link #2</a>',
                '<a href="https://localhost">Link #3</a>',
            ],
        ];

        $node = $this->createMock(NodeInterface::class);
        $node->expects($this->any())->method('getType')->willReturn(Type::HTML);
        $node->expects($this->any())->method('getSelector')->willReturn('strong');

        yield 'HTML type of undefined element' => [
            'node' => $node,
            'response' => $response,
            'expected' => [],
        ];

        $node = $this->createMock(NodeInterface::class);
        $node->expects($this->any())->method('getType')->willReturn(Type::ATTRIBUTE);
        $node->expects($this->any())->method('getSelector')->willReturn('meta[property="og:title"]');
        $node->expects($this->any())->method('getAttribute')->willReturn('content');

        yield 'Attribute type with single result' => [
            'node' => $node,
            'response' => $response,
            'expected' => [
                'Google title of article',
            ],
        ];

        $node = $this->createMock(NodeInterface::class);
        $node->expects($this->any())->method('getType')->willReturn(Type::ATTRIBUTE);
        $node->expects($this->any())->method('getSelector')->willReturn('strong');
        $node->expects($this->any())->method('getAttribute')->willReturn('content');

        yield 'Attribute type of undefined element' => [
            'node' => $node,
            'response' => $response,
            'expected' => [],
        ];

        $node = $this->createMock(NodeInterface::class);
        $node->expects($this->any())->method('getType')->willReturn(Type::ATTRIBUTE);
        $node->expects($this->any())->method('getSelector')->willReturn('meta');
        $node->expects($this->any())->method('getAttribute')->willReturn('data-id');

        yield 'Attribute type of undefined attribute name' => [
            'node' => $node,
            'response' => $response,
            'expected' => [],
        ];

        $node = $this->createMock(NodeInterface::class);
        $node->expects($this->any())->method('getType')->willReturn(Type::ATTRIBUTE);
        $node->expects($this->any())->method('getSelector')->willReturn('meta[property]');
        $node->expects($this->any())->method('getAttribute')->willReturn('content');

        yield 'Attribute type with few results' => [
            'node' => $node,
            'response' => $response,
            'expected' => [
                'Google title of article',
                'Google description of article',
            ],
        ];

        $node = $this->createMock(NodeInterface::class);
        $node->expects($this->any())->method('getType')->willReturn(Type::LARGEST_NESTED_CONTENT);
        $node->expects($this->any())->method('getSelector')->willReturn('body');

        yield 'Largest Nested Content type' => [
            'node' => $node,
            'response' => $response,
            'expected' => [
                'Title of article Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean porta nec urna a finibus. Etiam fermentum suscipit mi eu finibus. Donec risus diam, fringilla id varius vel, varius ac ipsum. Integer blandit eros in interdum fringilla. Sed vel dui vestibulum, varius tortor eget, laoreet est. Link #1 Link #2 Link #3',
            ],
        ];

        $node = $this->createMock(NodeInterface::class);
        $node->expects($this->any())->method('getType')->willReturn(Type::LARGEST_NESTED_CONTENT);
        $node->expects($this->any())->method('getSelector')->willReturn('nav');

        yield 'Largest Nested Content type of undefined element' => [
            'node' => $node,
            'response' => $response,
            'expected' => [],
        ];
    }
}
