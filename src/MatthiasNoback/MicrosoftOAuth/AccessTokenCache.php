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

    public function get(string $scope, string $grantType): string
    {
        $cacheKey = $this->generateCacheKey($scope, $grantType);

        return $this->cache->getItem($cacheKey)->get();
    }

    public function has(string $scope, string $grantType): bool
    {
        $cacheKey = $this->generateCacheKey($scope, $grantType);

        return $this->cache->hasItem($cacheKey);
    }

    public function set(string $scope, string $grantType, string $accessToken): bool
    {
        $cacheKey = $this->generateCacheKey($scope, $grantType);

        $cacheItem = $this->cache->getItem($cacheKey);
        $cacheItem->set($accessToken);
        $cacheItem->expiresAfter($this->lifetime);

        return $this->cache->save($cacheItem);
    }

    private function generateCacheKey(string $scope, string $grantType): string
    {
        return md5($scope.'_'.$grantType);
    }
}
