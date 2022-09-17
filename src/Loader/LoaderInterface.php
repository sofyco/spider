<?php declare(strict_types=1);

namespace Sofyco\Spider\Loader;

use Sofyco\Spider\ContextInterface;

interface LoaderInterface
{
    public function load(ContextInterface $context): string;
}
