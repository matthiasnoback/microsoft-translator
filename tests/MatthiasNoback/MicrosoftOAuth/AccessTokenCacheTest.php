<?php

namespace MatthiasNoback\Tests\MicrosoftOAuth;

use MatthiasNoback\MicrosoftOAuth\AccessTokenCache;

class AccessTokenCacheTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAccessToken()
    {
        $accessToken = 'theAccessToken';
        $usedCacheKey = null;
        $lifetime = 600;

        $cache = $this->createMockCache();
        $cache
            ->expects($this->once())
            ->method('save')
            ->with($this->isType('string'), $accessToken, $lifetime)
            ->will($this->returnValue(function($cacheKey) use (&$usedCacheKey) {
                $usedCacheKey = $cacheKey;

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
            ->method('contains')
            ->will($this->returnValue(true));

        $accessTokenCache = new AccessTokenCache($cache);

        $scope = 'theScope';
        $grantType = 'theGrantType';

        $this->assertTrue($accessTokenCache->has($scope, $grantType));
    }

    public function testGetAccessToken()
    {
        $accessToken = 'theAccessToken';

        $cache = $this->createMockCache();
        $cache
            ->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue($accessToken));


        $accessTokenCache = new AccessTokenCache($cache);

        $scope = 'theScope';
        $grantType = 'theGrantType';

        $actualAccessToken = $accessTokenCache->get($scope, $grantType);

        $this->assertSame($accessToken, $actualAccessToken);
    }

    private function createMockCache()
    {
        return $this->getMock('Doctrine\Common\Cache\Cache');
    }
}
