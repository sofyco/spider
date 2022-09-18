<?php declare(strict_types=1);

namespace Sofyco\Spider\Scraper;

use Sofyco\Spider\ContextInterface;
use Sofyco\Spider\Loader\LoaderInterface;

final class Scraper implements ScraperInterface
{
    public function __construct(private readonly LoaderInterface $loader)
    {
    }

    public function getResult(ContextInterface $context): string
    {
        return $this->loader->getContent($context);
    }
}
