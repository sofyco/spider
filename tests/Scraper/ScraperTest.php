<?php declare(strict_types=1);

namespace Sofyco\Spider\Tests\Scraper;

use PHPUnit\Framework\TestCase;
use Sofyco\Spider\ContextInterface;
use Sofyco\Spider\Loader\LoaderInterface;
use Sofyco\Spider\Scraper\Scraper;

final class ScraperTest extends TestCase
{
    public function testSuccessResult(): void
    {
        $url = 'https://localhost/example';
        $content = '<html>...</html>';

        $context = $this->createMock(ContextInterface::class);
        $context->expects($this->any())->method('getUrl')->willReturn($url);

        $loader = $this->createMock(LoaderInterface::class);
        $loader->expects($this->any())->method('getContent')->willReturn($content);

        $scraper = new Scraper($loader);

        self::assertSame($content, $scraper->getResult($context));
    }
}
