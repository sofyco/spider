<?php declare(strict_types=1);

namespace Sofyco\Spider;

final readonly class Context implements ContextInterface
{
    public function __construct(private string $url, private string $method = 'GET')
    {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
