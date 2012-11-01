<?php

namespace MatthiasNoback\MicrosoftOAuth;

use Doctrine\Common\Cache\Cache;

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
     * @param \Doctrine\Common\Cache\Cache $cache
     * @param int $lifetime
     */
    public function __construct(Cache $cache, $lifetime = 540)
    {
        $this->cache = $cache;
        $this->lifetime = $lifetime;
    }

    public function get($scope, $grantType)
    {
        $cacheKey = $this->generateCacheKey($scope, $grantType);

        return $this->cache->fetch($cacheKey);
    }

    public function has($scope, $grantType)
    {
        $cacheKey = $this->generateCacheKey($scope, $grantType);

        return $this->cache->contains($cacheKey);
    }

    public function set($scope, $grantType, $accessToken)
    {
        $cacheKey = $this->generateCacheKey($scope, $grantType);

        return $this->cache->save($cacheKey, $accessToken, $this->lifetime);
    }

    private function generateCacheKey($scope, $grantType)
    {
        return md5($scope.'_'.$grantType);
    }
}
