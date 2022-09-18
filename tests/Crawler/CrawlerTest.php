<?php declare(strict_types=1);

namespace Sofyco\Spider\Tests\Crawler;

use PHPUnit\Framework\TestCase;
use Sofyco\Spider\ContextInterface;
use Sofyco\Spider\Crawler\Crawler;
use Sofyco\Spider\Parser\Parser;
use Sofyco\Spider\Scraper\ScraperInterface;

final class CrawlerTest extends TestCase
{
    public function testSuccessResult(): void
    {
        $context = $this->createMock(ContextInterface::class);
        $context->expects($this->any())->method('getUrl')->willReturn('https://localhost/page1');

        $scraper = $this->createMock(ScraperInterface::class);
        $scraper
            ->expects($this->any())
            ->method('getResult')
            ->will($this->returnCallback(function (ContextInterface $context) {
                $filename = match ($context->getUrl()) {
                    'https://localhost/page1' => __DIR__ . '/stubs/page1.html',
                    'https://localhost/page2' => __DIR__ . '/stubs/page2.html',
                    'https://localhost/page3' => __DIR__ . '/stubs/page3.html',
                    default => throw new \InvalidArgumentException($context->getUrl()),
                };

                return \file_get_contents($filename);
            }));

        $crawler = new Crawler(scraper: $scraper, parser: new Parser());

        $index = 0;
        $expected = [
            ['url' => 'https://localhost/page2'],
            ['url' => 'https://localhost/page3'],
        ];

        foreach ($crawler->getResult($context) as $childContext) {
            self::assertSame($expected[$index]['url'], $childContext->getUrl());

            ++$index;
        }
    }
}
