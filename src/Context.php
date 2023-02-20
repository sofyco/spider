<?php declare(strict_types=1);

namespace Sofyco\Spider;

final class Context implements ContextInterface
{
    private ?string $content = null;

    public function __construct(private readonly string $url)
    {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }
}
