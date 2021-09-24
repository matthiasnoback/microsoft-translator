<?php

namespace MatthiasNoback\Buzz\Client;

use Buzz\Client\BuzzClientInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


/**
 * The CachedClient wraps an existing ClientInterface. It intercepts
 * requests and checks if the cache already constains a response for the
 * current request.
 *
 * When querying the cache, an invariant version of the request is used:
 * - Headers are sorted alphabetically
 * - Some headers that don't contribute to the variance of a request are ignored.
 */
class CachedClient implements BuzzClientInterface
{
    private $client;
    private $cache;
    private $lifetime;
    private $ignoreHeaders;
    private $hits = 0;
    private $misses = 0;

    /**
     * @param CacheItemPoolInterface       $cache         Cache for storing responses
     * @param \Buzz\Client\ClientInterface $client        Client to use for making HTTP requests
     * @param array                        $ignoreHeaders Headers to be ignored when determing request variance
     */
    public function __construct(BuzzClientInterface $client, CacheItemPoolInterface $cache, $lifetime = 0, array $ignoreHeaders = array())
    {
        $this->client = $client;
        $this->cache = $cache;
        $this->lifetime = $lifetime;
        $this->ignoreHeaders = $ignoreHeaders;
    }

    /**
     * Populates the supplied response with the response for the supplied request.
     *
     * @param RequestInterface $request  A request object
     * @param array            $options  BuzzClientInterface request options
     * @param MessageInterface $response A response object
     */
    public function sendRequest(RequestInterface $request, array $options = []): ResponseInterface
    {
        $cacheKey = $this->generateCacheKeyForPsr7Request($request);

        if ($this->cache->hasItem($cacheKey)) {
            $this->hits++;

            $cachedResponse = unserialize($this->cache->getItem($cacheKey)->get());
            return $cachedResponse;
        }

        $cacheItem = $this->cache->getItem($cacheKey);

        $this->misses++;

        $response = $this->client->sendRequest($request, $options);

        $cacheItem->set(serialize($response));
        $cacheItem->expiresAfter($this->lifetime);
        $this->cache->save($cacheItem);;

        return $response;
    }

    public function ignoreHeader($header)
    {
        $this->ignoreHeaders[] = $header;
    }

    /**
     * Get the number of times the cache already contained a response, and thus,
     * no real HTTP request made
     *
     * @return int
     */
    public function getHits(): int
    {
        return $this->hits;
    }

    /**
     * Get the number of times the cache did not contain a response and an HTTP
     * request was made to fetch the response
     *
     * @return int
     */
    public function getMisses(): int
    {
        return $this->misses;
    }

    /**
     * Generate a unique key for the given request
     *
     * @see CachedClient::__construct()
     *
     * @param RequestInterface $request
     * @return string
     */
    private function generateCacheKeyForPsr7Request(RequestInterface $request): string
    {
        $normalizedRequest = $this->getNormalizedRequest($request);
        return md5(serialize($normalizedRequest));
    }

    /**
     * Remove ignored headers from request object
     *
     * @param RequestInterface $request
     * @return mixed
     */
    private function getNormalizedRequest(RequestInterface $request): RequestInterface
    {
        $normalizedRequest = clone $request;

        $ignoreHeaders = $this->ignoreHeaders;

        foreach ($ignoreHeaders as $ignoreHeader) {
            if ($normalizedRequest->hasHeader($ignoreHeader)) {
                $normalizedRequest = $normalizedRequest->withoutHeader($ignoreHeader);
            }
        }

        return $normalizedRequest;
    }
}
