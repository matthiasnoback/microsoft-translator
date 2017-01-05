<?php

namespace MatthiasNoback\Tests\MicrosoftOAuth;

use MatthiasNoback\MicrosoftOAuth\AzureTokenProvider;

class AzuresTokenProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTokenWithoutCache()
    {
        $azureKey = 'azureKey';
        $accessToken = 'accessToken';
        $scope = 'theScope';
        $grantType = 'theGrantType';

        $response = $this->createMockResponse($accessToken);

        $browser = $this->createMockBrowser();
        $browser
            ->expects($this->once())
            ->method('post')
            ->with(
                'https://api.cognitive.microsoft.com/sts/v1.0/issueToken?Subscription-Key=' . $azureKey,
                array('Content-Length' => 0)
            )
            ->will($this->returnValue($response));
        $accessTokenProvider = new AzureTokenProvider($browser, $azureKey);

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
        $response = $this->createMockResponse($accessToken);
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


        $accessTokenProvider = new AzureTokenProvider($browser, 'azureKey');
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

        $accessTokenProvider = new AzureTokenProvider($browser, 'azureKey');
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
