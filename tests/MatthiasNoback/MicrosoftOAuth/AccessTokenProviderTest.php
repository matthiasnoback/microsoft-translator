<?php

namespace MatthiasNoback\Tests\MicrosoftOAuth;

use MatthiasNoback\MicrosoftOAuth\AccessTokenProvider;

class AccessTokenProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTokenWithoutCache()
    {
        $clientId = 'theClientId';
        $clientSecret = 'theClientSecret';
        $scope = 'theScope';
        $grantType = 'theGrantType';

        $accessToken = 'accessToken';

        $response = $this->createMockResponse('{"access_token":"'.$accessToken.'"}');

        $browser = $this->createMockBrowser();
        $browser
            ->expects($this->once())
            ->method('post')
            ->with(
                'https://datamarket.accesscontrol.windows.net/v2/OAuth2-13',
                array(),
                'client_id='.$clientId.'&client_secret='.$clientSecret.'&scope='.$scope.'&grant_type='.$grantType
            )
            ->will($this->returnValue($response));

        $accessTokenProvider = new AccessTokenProvider($browser, $clientId, $clientSecret);

        $actualAccessToken = $accessTokenProvider->getAccessToken($scope, $grantType);

        $this->assertSame($accessToken, $actualAccessToken);
    }

    public function testGetTokenWithCacheMiss()
    {
        $scope = 'theScope';
        $grantType = 'theGrantType';

        $accessToken = 'accessToken';

        // the cache does not contain an access token yet
        $accessTokenCache = $this->createMockAccessTokenCache();
        $accessTokenCache
            ->expects($this->once())
            ->method('has')
            ->with($scope, $grantType)
            ->will($this->returnValue(false));

        // the browser will used to fetch a fresh access token
        $response = $this->createMockResponse('{"access_token":"'.$accessToken.'"}');
        $browser = $this->createMockBrowser();
        $browser
            ->expects($this->once())
            ->method('post')
            ->will($this->returnValue($response));

        // finally, the access token should be stored in the cache
        $accessTokenCache
            ->expects($this->once())
            ->method('set')
            ->with($scope, $grantType, $accessToken);


        $accessTokenProvider = new AccessTokenProvider($browser, 'theClientId', 'theClientSecret');
        $accessTokenProvider->setCache($accessTokenCache);

        $actualAccessToken = $accessTokenProvider->getAccessToken($scope, $grantType);

        $this->assertSame($accessToken, $actualAccessToken);
    }

    public function testGetTokenWithCacheHit()
    {
        $scope = 'theScope';
        $grantType = 'theGrantType';

        $accessToken = 'accessToken';

        // the cache already contains an access token
        $accessTokenCache = $this->createMockAccessTokenCache();
        $accessTokenCache
            ->expects($this->once())
            ->method('has')
            ->with($scope, $grantType)
            ->will($this->returnValue(true));

        // the browser should not be used
        $browser = $this->createMockBrowser();
        $browser
            ->expects($this->never())
            ->method('post');

        // the access token will be retrieved from the cache
        $accessTokenCache
            ->expects($this->once())
            ->method('get')
            ->with($scope, $grantType)
            ->will($this->returnValue($accessToken));

        $accessTokenProvider = new AccessTokenProvider($browser, 'theClientId', 'theClientSecret');
        $accessTokenProvider->setCache($accessTokenCache);

        $accessTokenProvider->getAccessToken($scope, $grantType);
    }

    private function createMockBrowser()
    {
        return $this
            ->getMockBuilder('Buzz\\Browser')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function createMockResponse($content)
    {
        $response = $this->getMock('Buzz\Message\Response');
        $response
            ->expects($this->any())
            ->method('getContent')
            ->will($this->returnValue($content));

        $response
            ->expects($this->any())
            ->method('isSuccessful')
            ->will($this->returnValue(true));

        return $response;
    }

    private function createMockAccessTokenCache()
    {
        return $this->getMock('MatthiasNoback\MicrosoftOAuth\AccessTokenCacheInterface');
    }
}
