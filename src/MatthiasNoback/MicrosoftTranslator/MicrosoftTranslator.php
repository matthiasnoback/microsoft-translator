<?php

namespace MatthiasNoback\MicrosoftTranslator;

use MatthiasNoback\MicrosoftOAuth\AccessTokenProviderInterface;
use MatthiasNoback\MicrosoftTranslator\ApiCall;

use Buzz\Browser;

class MicrosoftTranslator
{
    const ACCESS_TOKEN_SCOPE = 'http://api.microsofttranslator.com';
    const ACCESS_TOKEN_GRANT_TYPE = 'client_credentials';

    /**
     * @var \Buzz\Browser
     */
    private $browser;

    /**
     * @var \MatthiasNoback\MicrosoftOAuth\AccessTokenProviderInterface
     */
    private $accessTokenProvider;

    public function __construct(Browser $browser, AccessTokenProviderInterface $accessTokenProvider)
    {
        $this->browser = $browser;
        $this->accessTokenProvider = $accessTokenProvider;
    }

    public function translate($text, $to, $from = null, $category = null)
    {
        $apiCall = new ApiCall\Translate($text, $to, $from, $category);

        return $this->call($apiCall);
    }

    public function translateArray(array $texts, $to, $from = null)
    {
        $apiCall = new ApiCall\TranslateArray($texts, $to, $from);

        return $this->call($apiCall);
    }

    public function detect($text)
    {
        $apiCall = new ApiCall\Detect($text);

        return $this->call($apiCall);
    }

    public function detectArray(array $texts)
    {
        $apiCall = new ApiCall\DetectArray($texts);

        return $this->call($apiCall);
    }

    public function breakSentences($text, $language)
    {
        $apiCall = new ApiCall\BreakSentences($text, $language);

        return $this->call($apiCall);
    }

    public function speak($text, $language, $format = null, $options = null)
    {
        $apiCall = new ApiCall\Speak($text, $language, $format, $options);

        return $this->call($apiCall);
    }

    public function getLanguagesForSpeak()
    {
        $apiCall = new ApiCall\GetLanguagesForSpeak();

        return $this->call($apiCall);
    }

    public function getLanguagesForTranslate()
    {
        $apiCall = new ApiCall\GetLanguagesForTranslate();

        return $this->call($apiCall);
    }

    public function getLanguageNames(array $languageCodes, $locale)
    {
        $apiCall = new ApiCall\GetLanguageNames($languageCodes, $locale);

        return $this->call($apiCall);
    }

    /**
     * @param \MatthiasNoback\MicrosoftTranslator\ApiCall\ApiCallInterface $apiCall
     */
    private function call(ApiCall\ApiCallInterface $apiCall)
    {
        $url = $apiCall->getUrl();
        $method = $apiCall->getHttpMethod();
        $headers = array(
            'Authorization: Bearer '.$this->getAccessToken(),
            'Content-Type: text/xml',
        );
        $content = $apiCall->getRequestContent();

        var_dump($content);

        $response = $this->browser->call($url, $method, $headers, $content);

        if (!$response->isSuccessful()) {
            throw new \RuntimeException(sprintf(
                'API call was not successful, %d: %s',
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));
        }

        /* @var $response \Buzz\Message\Response */

        $responseContent = $response->getContent();

        var_dump($responseContent); exit;

        return $apiCall->parseResponse($responseContent);
    }

    private function getAccessToken()
    {
        return $this->accessTokenProvider->getAccessToken(self::ACCESS_TOKEN_SCOPE, self::ACCESS_TOKEN_GRANT_TYPE);
    }
}
