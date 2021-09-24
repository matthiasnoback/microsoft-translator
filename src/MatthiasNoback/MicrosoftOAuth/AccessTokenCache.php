<?php

namespace MatthiasNoback\MicrosoftOAuth;

use Psr\Cache\CacheItemPoolInterface;

class AccessTokenCache implements AccessTokenCacheInterface
{
    private $cache;
    private $lifetime;

    /**
     * The AccessTokenCache requires a Cache instance and optionally a
     * lifetime for access tokens. Microsoft mentions a lifetime of 10
     * minutes, but for safety's sake, let's assume a lifetime of 9
     * minutes.
     *
     * @param CacheItemPoolInterface $cache
     * @param int $lifetime
     */
    public function __construct(CacheItemPoolInterface $cache, $lifetime = 540)
    {
        $this->cache = $cache;
        $this->lifetime = $lifetime;
    }

    public function get($scope, $grantType)
    {
        $cacheKey = $this->generateCacheKey($scope, $grantType);

        return $this->cache->getItem($cacheKey)->get();
    }

    public function has($scope, $grantType)
    {
        $cacheKey = $this->generateCacheKey($scope, $grantType);

        return $this->cache->hasItem($cacheKey);
    }

    public function set($scope, $grantType, $accessToken)
    {
        $cacheKey = $this->generateCacheKey($scope, $grantType);

        $cacheItem = $this->cache->getItem($cacheKey);
        $cacheItem->set($accessToken);
        $cacheItem->expiresAfter($this->lifetime);

        return $this->cache->save($cacheItem);
    }

    private function generateCacheKey($scope, $grantType)
    {
        return md5($scope.'_'.$grantType);
    }
}
