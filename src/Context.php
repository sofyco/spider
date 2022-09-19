<?php declare(strict_types=1);

namespace Sofyco\Spider;

final class Context implements ContextInterface
{
    public function __construct(private readonly string $url, private readonly int $expiresAfter = 3600)
    {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getExpiresAfter(): int
    {
        return $this->expiresAfter;
    }
}
