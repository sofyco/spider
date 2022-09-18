<?php declare(strict_types=1);

namespace Sofyco\Spider\Parser\Builder;

interface NodeInterface
{
    public function getType(): Node\Type;

    public function getSelector(): string;

    public function getAttribute(): ?string;
}
