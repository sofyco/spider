<?php declare(strict_types=1);

namespace Sofyco\Spider\Scraper;

use Sofyco\Spider\ContextInterface;

final readonly class Scraper implements ScraperInterface
{
    public function getResult(ContextInterface $context): string
    {
        $handle = curl_init(url: $context->getUrl());

        curl_setopt(handle: $handle, option: CURLOPT_RETURNTRANSFER, value: true);
        curl_setopt(handle: $handle, option: CURLOPT_FOLLOWLOCATION, value: true);

        $response = curl_exec(handle: $handle);
        $error = curl_error(handle: $handle);
        curl_close(handle: $handle);

        if (false === is_string($response)) {
            throw new \RuntimeException(message: 'cURL error: ' . $error);
        }

        return $response;
    }
}
