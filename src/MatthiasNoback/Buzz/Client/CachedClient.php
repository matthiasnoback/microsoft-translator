<?php

namespace MatthiasNoback\Buzz\Client;

use Buzz\Client\ClientInterface;
use Doctrine\Common\Cache\Cache;
use Buzz\Message\RequestInterface;
use Buzz\Message\MessageInterface;

/**
 * The CachedClient wraps an existing ClientInterface. It intercepts
 * requests and checks if the cache already constains a response for the
 * current request.
 *
 * When querying the cache, an invariant version of the request is used:
 * - Headers are sorted alphabetically
 * - Some headers that don't contribute to the variance of a request are ignored.
 */
class CachedClient implements ClientInterface
{
    private $client;
    private $cache;
    private $lifetime;
    private $ignoreHeaders;
    private $hits = 0;
    private $misses = 0;

    /**
     * @param \Doctrine\Common\Cache\Cache $cache         Cache for storing responses
     * @param \Buzz\Client\ClientInterface $client        Client to use for making HTTP requests
     * @param array                        $ignoreHeaders Headers to be ignored when determing request variance
     */
    public function __construct(ClientInterface $client, Cache $cache, $lifetime = 0, array $ignoreHeaders = array())
    {
        $this->client = $client;
        $this->cache = $cache;
        $this->lifetime = $lifetime;
        $this->ignoreHeaders = $ignoreHeaders;
    }

    public function ignoreHeader($header)
    {
        $this->ignoreHeaders[] = $header;
    }

    /**
     * Populates the supplied response with the response for the supplied request.
     *
     * @param RequestInterface $request  A request object
     * @param MessageInterface $response A response object
     */
    public function send(RequestInterface $request, MessageInterface $response)
    {
        $cacheKey = $this->generateCacheKeyForRequest($request);

        if ($this->cache->contains($cacheKey)) {
            $this->hits++;

            $cachedResponse = unserialize($this->cache->fetch($cacheKey));
            /* @var $cachedResponse \Buzz\Message\Response */

            $response->setContent($cachedResponse->getContent());
            $response->setHeaders($cachedResponse->getHeaders());

            return;
        }

        $this->misses++;

        $this->client->send($request, $response);

        $this->cache->save($cacheKey, serialize($response), $this->lifetime);
    }

    /**
     * Get the number of times the cache already contained a response, and thus,
     * no real HTTP request made
     *
     * @return int
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * Get the number of times the cache did not contain a response and an HTTP
     * request was made to fetch the response
     *
     * @return int
     */
    public function getMisses()
    {
        return $this->misses;
    }

    /**
     * Generate a unique key for the given request
     *
     * The request is made invariant by sorting the headers alphabetically and by
     * removing headers that are to be ignored.
     *
     * @see CachedClient::__construct()
     *
     * @param \Buzz\Message\RequestInterface $request
     * @return string
     */
    private function generateCacheKeyForRequest(RequestInterface $request)
    {
        $normalizedRequest = $this->getNormalizedRequest($request);

        return md5($normalizedRequest->__toString());
    }

    /**
     * Reduces the request to its normal form
     * Which means: strip all information that does not contribute to its uniqueness
     * This will prevent cache misses, when effectively indifferent requests are made
     *
     * @param \Buzz\Message\RequestInterface $request
     * @return \Buzz\Message\RequestInterface
     */
    private function getNormalizedRequest(RequestInterface $request)
    {
        $normalizedRequest = clone $request;

        $headers = $request->getHeaders();
        $normalizedHeaders = $this->normalizeHeaders($headers);
        asort($normalizedHeaders);

        $normalizedRequest->setHeaders($normalizedHeaders);

        return $normalizedRequest;
    }

    /**
     * Get only those headers that should not be ignored
     *
     * @param array $headers
     * @return array
     */
    private function normalizeHeaders(array $headers)
    {
        $ignoreHeaders = $this->ignoreHeaders;

        foreach ($ignoreHeaders as $ignoreHeader) {
            $headers = array_filter($headers, function($header) use ($ignoreHeader) {
                return stripos($header, $ignoreHeader.':') !== 0;
            });
        }

        return $headers;
    }
}
