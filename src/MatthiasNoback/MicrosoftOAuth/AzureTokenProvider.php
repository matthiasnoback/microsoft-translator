<?php

namespace MatthiasNoback\MicrosoftOAuth;

use Buzz\Browser;
use MatthiasNoback\Exception\RequestFailedException;
use MatthiasNoback\Exception\InvalidResponseException;

class AzureTokenProvider implements AccessTokenProviderInterface
{

	/**
	 * @var string The auth url
	 */
	private $auth_url = 'https://api.cognitive.microsoft.com/sts/v1.0/issueToken';

    /**
     * @var \Buzz\Browser
     */
    private $browser;

    /**
     * @var string The client id of your application
     */
    private $azureKey;

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
	 * @param string $azureKey       The azure key for Translator service
	 * @param string|null $authUrl       The custom uth url
	 */
	public function __construct(Browser $browser, $azureKey, $authUrl = null)
	{
		$this->browser = $browser;
		$this->azureKey = $azureKey;
		if(!empty($authUrl))
		{
			$this->auth_url = $authUrl;
		}
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
        try {
            $response = $this->browser->post(
                $this->auth_url . "?Subscription-Key=" . urlencode($this->azureKey),
                array('Content-Length' => 0)
            );
        }
        catch (\Exception $previous) {
            throw new RequestFailedException(sprintf(
                'Request failed: %s',
                $previous->getMessage()
            ), 0, $previous);
        }

        if ($response->getStatusCode() !== 200) {
            throw new RequestFailedException(sprintf(
                'Call to Auth server failed, %d: %s',
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));
        }

        return $response->getBody()->getContents();
    }
}
