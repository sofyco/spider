<?php declare(strict_types=1);

namespace Sofyco\Spider\Tests\Crawler;

use PHPUnit\Framework\TestCase;
use Sofyco\Spider\Context;
use Sofyco\Spider\ContextInterface;
use Sofyco\Spider\Crawler\Crawler;
use Sofyco\Spider\Parser\Builder\Node;
use Sofyco\Spider\Parser\Parser;
use Sofyco\Spider\Scraper\ScraperInterface;

final class CrawlerTest extends TestCase
{
    public function testSuccessResult(): void
    {
        $context = new Context(url: 'https://localhost/page1');

        $scraper = $this->createMock(ScraperInterface::class);
        $scraper
            ->expects($this->any())
            ->method('getResult')
            ->willReturnCallback(function (ContextInterface $context) {
                $filename = match ($context->getUrl()) {
                    'https://localhost/page1' => __DIR__ . '/stubs/page1.html',
                    'https://localhost/page2' => __DIR__ . '/stubs/page2.html',
                    'https://localhost/page3' => __DIR__ . '/stubs/page3.html',
                    'https://localhost/page3?query=page5' => __DIR__ . '/stubs/page3.html',
                    'https://localhost/page4' => __DIR__ . '/stubs/page4.html',
                    default => throw new \InvalidArgumentException($context->getUrl()),
                };

                return \file_get_contents($filename);
            });

        $crawler = new Crawler(scraper: $scraper, parser: new Parser());

        $node = new Node(type: Node\Type::ATTRIBUTE, selector: 'a', attribute: 'href');
        $result = $crawler->getResult(context: $context, node: $node);
        $links = [];

        foreach ($result as $value) {
            $links[] = $value->getUrl();
        }

        $expected = [
            'https://localhost/page2',
            'https://localhost/page3',
            'https://localhost/page4',
            'https://localhost/page3?query=page5',
        ];

        self::assertSame($expected, $links);
    }
}
