<?php

namespace MatthiasNoback\MicrosoftOAuth;

use Buzz\Browser;
use MatthiasNoback\Exception\RequestFailedException;
use MatthiasNoback\Exception\InvalidResponseException;

class AccessTokenProvider implements AccessTokenProviderInterface
{
    const OAUTH_URL = 'https://datamarket.accesscontrol.windows.net/v2/OAuth2-13';

    /**
     * @var \Buzz\Browser
     */
    private $browser;

    /**
     * @var string The client id of your application
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var AccessTokenCacheInterface|null
     */
    private $accessTokenCache;

    /**
     * The AccessTokenProvider requires a Buzz browser instance, and both a client id
     * and a client secret. You can obtain these by registering your application
     * at https://datamarket.azure.com/developer/applications
     *
     * @param \Buzz\Browser $browser The browser to use for fetching access tokens
     * @param string $clientId       The client id of your application
     * @param string $clientSecret   The client secret of your application
     */
    public function __construct(Browser $browser, $clientId, $clientSecret)
    {
        $this->browser = $browser;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function setCache(AccessTokenCacheInterface $accessTokenCache)
    {
        $this->accessTokenCache = $accessTokenCache;
    }

    public function getAccessToken($scope, $grantType)
    {
        if ($this->accessTokenCache !== null && $this->accessTokenCache->has($scope, $grantType)) {
            return $this->accessTokenCache->get($scope, $grantType);
        }

        $accessToken = $this->authorize($scope, $grantType);

        if ($this->accessTokenCache !== null) {
            $this->accessTokenCache->set($scope, $grantType, $accessToken);
        }

        return $accessToken;
    }

    private function authorize($scope, $grantType)
    {
        $requestParameters = array(
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope'         => $scope,
            'grant_type'    => $grantType,
        );

        try {
            $response = $this->browser->post(
                self::OAUTH_URL,
                array(),
                http_build_query($requestParameters, null, '&')
            );
        }
        catch (\Exception $previous) {
            throw new RequestFailedException(sprintf(
                'Request failed: %s',
                $previous->getMessage()
            ), null, $previous);
        }

        if (!$response->isSuccessful()) {
            throw new RequestFailedException(sprintf(
                'Call to OAuth server failed, %d: %s',
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));
        }

        /* @var $response \Buzz\Message\Response */

        $result = json_decode($response->getContent(), true);

        if (isset($result['error']) && $result['error'] !== '') {
            throw new RequestFailedException(sprintf('Response contains an error: %s', $result['error']));
        }

        if (!isset($result['access_token'])) {
            throw new InvalidResponseException('Response contains no access token');
        }

        return $result['access_token'];
    }
}
