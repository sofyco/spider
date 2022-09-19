<?php declare(strict_types=1);

namespace Sofyco\Spider\Scraper;

use Sofyco\Spider\ContextInterface;
use Sofyco\Spider\Loader\LoaderInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class Scraper implements ScraperInterface
{
    public function __construct(private readonly LoaderInterface $loader, private readonly CacheInterface $cache)
    {
    }

    public function getResult(ContextInterface $context): string
    {
        $value = $this->cache->get(\md5($context->getUrl()), function (ItemInterface $item) use ($context) {
            $item->expiresAfter($context->getExpiresAfter());

            return $this->loader->getContent($context);
        });

        return \is_string($value) ? $value : '';
    }
}
