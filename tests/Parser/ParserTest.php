<?php declare(strict_types=1);

namespace Sofyco\Spider\Tests\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sofyco\Spider\Parser\Builder\Node;
use Sofyco\Spider\Parser\Builder\Node\Type;
use Sofyco\Spider\Parser\Builder\NodeInterface;
use Sofyco\Spider\Parser\Exception\UnexpectedTypeException;
use Sofyco\Spider\Parser\Parser;

final class ParserTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        restore_exception_handler();
    }

    #[DataProvider('typeProvider')]
    public function testTypeResult(NodeInterface $node, string $response, array $expected): void
    {
        $parser = new Parser();
        $result = $parser->getResult($response, $node);

        if (empty($expected)) {
            self::assertEmpty(iterator_to_array($result));
        } else {
            /** @var int $index */
            foreach ($result as $index => $value) {
                self::assertSame($expected[$index], $value);
            }
        }
    }

    public static function typeProvider(): iterable
    {
        $response = (string) file_get_contents(__DIR__ . '/stubs/index.html');

        yield 'Text type' => [
            'node' => new Node(type: Type::TEXT, selector: 'a'),
            'response' => $response,
            'expected' => [
                'Link #1',
                'Link #2',
                'Link #3',
            ],
        ];

        yield 'Text type of undefined element' => [
            'node' => new Node(type: Type::TEXT, selector: 'strong'),
            'response' => $response,
            'expected' => [],
        ];

        yield 'HTML type' => [
            'node' => new Node(type: Type::HTML, selector: 'ul li a'),
            'response' => $response,
            'expected' => [
                '<a href="https://localhost">Link #1</a>',
                '<a href="https://localhost">Link #2</a>',
                '<a href="https://localhost">Link #3</a>',
            ],
        ];

        yield 'HTML type of undefined element' => [
            'node' => new Node(type: Type::HTML, selector: 'strong'),
            'response' => $response,
            'expected' => [],
        ];

        yield 'Attribute type with single result' => [
            'node' => new Node(type: Type::ATTRIBUTE, selector: 'meta[property="og:title"]', attribute: 'content'),
            'response' => $response,
            'expected' => [
                'Google title of article',
            ],
        ];

        yield 'Attribute type of undefined element' => [
            'node' => new Node(type: Type::ATTRIBUTE, selector: 'strong', attribute: 'content'),
            'response' => $response,
            'expected' => [],
        ];

        yield 'Attribute type of undefined attribute name' => [
            'node' => new Node(type: Type::ATTRIBUTE, selector: 'meta', attribute: 'data-id'),
            'response' => $response,
            'expected' => [],
        ];

        yield 'Attribute type with empty attribute name' => [
            'node' => new Node(type: Type::ATTRIBUTE, selector: 'meta'),
            'response' => $response,
            'expected' => [],
        ];

        yield 'Attribute type with few results' => [
            'node' => new Node(type: Type::ATTRIBUTE, selector: 'meta[property]', attribute: 'content'),
            'response' => $response,
            'expected' => [
                'Google title of article',
                'Google description of article',
            ],
        ];

        yield 'Largest Nested Content type' => [
            'node' => new Node(type: Type::LARGEST_NESTED_CONTENT, selector: 'body'),
            'response' => $response,
            'expected' => [
                'Donec risus diam, fringilla id varius vel, varius ac ipsum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer blandit eros in interdum fringilla. Integer blandit eros in interdum fringilla.',
            ],
        ];

        yield 'Largest Nested Content type in article' => [
            'node' => new Node(type: Type::LARGEST_NESTED_CONTENT, selector: 'article'),
            'response' => $response,
            'expected' => [
                'Donec risus diam, fringilla id varius vel, varius ac ipsum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer blandit eros in interdum fringilla. Integer blandit eros in interdum fringilla.',
            ],
        ];

        yield 'Largest Nested Content type of undefined element' => [
            'node' => new Node(type: Type::LARGEST_NESTED_CONTENT, selector: 'nav'),
            'response' => $response,
            'expected' => [],
        ];

        $response = (string) \file_get_contents(__DIR__ . '/stubs/rss_simple.xml');

        yield 'RSS XML type Article titles' => [
            'node' => new Node(type: Type::XML, selector: '//rss/channel/item/title'),
            'response' => $response,
            'expected' => [
                'RSS Item #1',
                'RSS Item #2',
                'RSS Item #3',
            ],
        ];

        yield 'RSS XML type Article tags' => [
            'node' => new Node(type: Type::XML, selector: '//rss/channel/item/category'),
            'response' => $response,
            'expected' => [
                'Sport',
                'Ukraine',
                'Europe',
                'Women\'s football',
                'Football',
                'Sport',
                'Football',
                'Sport',
            ],
        ];

        yield 'RSS Text type Article links' => [
            'node' => new Node(type: Type::TEXT, selector: 'rss channel item guid'),
            'response' => $response,
            'expected' => [
                'https://www.localhost.com/section/page-one',
                'https://www.localhost.com/section/page-two',
                'https://www.localhost.com/section/page-three',
            ],
        ];

        yield 'RSS Attribute type Article images' => [
            'node' => new Node(type: Type::ATTRIBUTE, selector: 'rss channel item *[width="460"]', attribute: 'url'),
            'response' => $response,
            'expected' => [
                'https://www.localhost.com/image-2.png',
                'https://www.localhost.com/image-4.png',
                'https://www.localhost.com/image-6.png',
            ],
        ];

        yield 'XML type of undefined element' => [
            'node' => new Node(type: Type::XML, selector: '//rss/channel/item/abc'),
            'response' => $response,
            'expected' => [],
        ];
    }

    public function testUnexpectedType(): void
    {
        $node = new Node(type: Type::TEXT, selector: '');
        $parser = new Parser();

        $property = new \ReflectionProperty(class: Parser::class, property: 'map');
        $property->setValue($parser, new \WeakMap());

        $this->expectException(UnexpectedTypeException::class);

        $result = $parser->getResult('', $node);

        iterator_to_array($result);
    }
}
