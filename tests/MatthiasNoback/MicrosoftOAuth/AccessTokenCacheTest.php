<?php

namespace MatthiasNoback\Tests\MicrosoftOAuth;

use PHPUnit\Framework\TestCase;
use MatthiasNoback\MicrosoftOAuth\AccessTokenCache;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class AccessTokenCacheTest extends TestCase
{
    public function testSetAccessToken()
    {
        $accessToken = 'theAccessToken';
        $usedCacheKey = null;
        $lifetime = 600;

        $cacheItem = $this->createMockCacheItem();
        $cacheItem->expects($this->once())
            ->method('getKey')
            ->will($this->returnValue('itemKey'));

        $cache = $this->createMockCache();
        $cache
            ->expects($this->once())
            ->method('getItem')
            ->will($this->returnValue($cacheItem));

        $cache
            ->expects($this->once())
            ->method('save')
            ->with($cacheItem)
            ->will($this->returnCallback(function($cacheItem) use (&$usedCacheKey) {
                $usedCacheKey = $cacheItem->getKey();

                return true;
            }));

        $accessTokenCache = new AccessTokenCache($cache, $lifetime);

        $scope = 'theScope';
        $grantType = 'theGrantType';
        $accessTokenCache->set($scope, $grantType, $accessToken);
    }

    public function testHasAccessToken()
    {
        $cache = $this->createMockCache();
        $cache
            ->expects($this->once())
            ->method('hasItem')
            ->will($this->returnValue(true));

        $accessTokenCache = new AccessTokenCache($cache);

        $scope = 'theScope';
        $grantType = 'theGrantType';

        $this->assertTrue($accessTokenCache->has($scope, $grantType));
    }

    public function testGetAccessToken()
    {
        $accessToken = 'theAccessToken';

        $cacheItem = $this->createMockCacheItem();

        $cacheItem
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($accessToken));

        $cache = $this->createMockCache();
        $cache
            ->expects($this->once())
            ->method('getItem')
            ->will($this->returnValue($cacheItem));



        $accessTokenCache = new AccessTokenCache($cache);

        $scope = 'theScope';
        $grantType = 'theGrantType';

        $actualAccessToken = $accessTokenCache->get($scope, $grantType);

        $this->assertSame($accessToken, $actualAccessToken);
    }

    private function createMockCache()
    {
        return $this->getMockBuilder(CacheItemPoolInterface::class)->getMock();
    }

    private function createMockCacheItem()
    {
        return $this->getMockBuilder(CacheItemInterface::class)->getMock();
    }
}
