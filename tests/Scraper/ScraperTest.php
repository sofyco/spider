<?php declare(strict_types=1);

namespace Sofyco\Spider\Tests\Scraper;

use PHPUnit\Framework\TestCase;
use Sofyco\Spider\Context;
use Sofyco\Spider\Loader\LoaderInterface;
use Sofyco\Spider\Scraper\Scraper;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class ScraperTest extends TestCase
{
    public function testSuccessResult(): void
    {
        $url = 'https://localhost/example';
        $content = '<html>...</html>';

        $loader = $this->createMock(LoaderInterface::class);
        $loader->expects($this->any())->method('getContent')->willReturn($content);

        $cache = new ArrayAdapter();
        $context = new Context(url: $url);
        $scraper = new Scraper($loader, $cache);

        self::assertSame($content, $scraper->getResult($context));
    }
}
