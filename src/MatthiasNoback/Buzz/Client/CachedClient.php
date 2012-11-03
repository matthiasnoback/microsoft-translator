<?php

namespace MatthiasNoback\Buzz\Client;

use Buzz\Client\ClientInterface;
use Doctrine\Common\Cache\Cache;
use Buzz\Message\RequestInterface;
use Buzz\Message\MessageInterface;

class CachedClient implements ClientInterface
{
    private $cache;
    private $hits = 0;
    private $misses = 0;

    public function __construct(Cache $cache, ClientInterface $client)
    {
        $this->cache = $cache;
        $this->client = $client;
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

        $this->cache->save($cacheKey, serialize($response));
    }

    public function getHits()
    {
        return $this->hits;
    }

    public function getMisses()
    {
        return $this->misses;
    }

    private function generateCacheKeyForRequest(RequestInterface $request)
    {
        return md5($request->__toString());
    }
}
