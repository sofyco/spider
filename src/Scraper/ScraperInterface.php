<?php declare(strict_types=1);

namespace Sofyco\Spider\Scraper;

use Sofyco\Spider\ContextInterface;

/**
 * Scraper visits web pages and retrieves relevant data to store into a data storage.
 */
interface ScraperInterface
{
    public function getResult(ContextInterface $context): string;
}
