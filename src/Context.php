<?php declare(strict_types=1);

namespace Sofyco\Spider;

final class Context implements ContextInterface
{
    public function __construct(private readonly string $url)
    {
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
