<?php declare(strict_types=1);

namespace Sofyco\Spider\Tests\Scraper;

use PHPUnit\Framework\TestCase;
use Sofyco\Spider\Context;
use Sofyco\Spider\Scraper\Enum\HttpMethod;
use Sofyco\Spider\Scraper\Scraper;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class ScraperTest extends TestCase
{
    public function testSuccessResult(): void
    {
        $content = '<html>...</html>';
        $context = new Context(url: 'https://localhost/example');

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getContent')
            ->willReturn($content);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with(HttpMethod::GET->value, $context->getUrl())
            ->willReturn($response);

        $scraper = new Scraper(httpClient: $httpClient);

        self::assertSame($content, $scraper->getResult($context));
    }
}
