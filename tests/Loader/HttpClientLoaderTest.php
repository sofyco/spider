<?php declare(strict_types=1);

namespace Sofyco\Spider\Tests\Loader;

use PHPUnit\Framework\TestCase;
use Sofyco\Spider\ContextInterface;
use Sofyco\Spider\Loader\HttpClientLoader;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class HttpClientLoaderTest extends TestCase
{
    public function testSuccessResult(): void
    {
        $content = '<html>...</html>';

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->any())->method('getContent')->willReturn($content);

        $client = $this->createMock(HttpClientInterface::class);
        $client->expects($this->any())->method('request')->willReturn($response);

        $context = $this->createMock(ContextInterface::class);
        $context->expects($this->any())->method('getUrl')->willReturn('https://localhost/page1');

        $loader = new HttpClientLoader($client);

        self::assertSame($content, $loader->getContent($context));
    }
}
