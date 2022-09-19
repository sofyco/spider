<?php declare(strict_types=1);

namespace Sofyco\Spider;

interface ContextInterface
{
    public function getUrl(): string;

    public function getExpiresAfter(): int;
}
