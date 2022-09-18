<?php declare(strict_types=1);

namespace Sofyco\Spider\Parser\Builder;

final class Node implements NodeInterface
{
    public function __construct(private readonly Node\Type $type, private readonly string $selector, private readonly ?string $attribute)
    {
    }

    public function getType(): Node\Type
    {
        return $this->type;
    }

    public function getSelector(): string
    {
        return $this->selector;
    }

    public function getAttribute(): ?string
    {
        return $this->attribute;
    }
}
