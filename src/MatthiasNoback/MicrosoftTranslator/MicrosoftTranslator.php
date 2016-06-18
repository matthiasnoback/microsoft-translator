<?php

namespace MatthiasNoback\MicrosoftTranslator;

use Buzz\Browser;
use MatthiasNoback\Exception\RequestFailedException;
use MatthiasNoback\MicrosoftOAuth\AccessTokenProviderInterface;
use MatthiasNoback\MicrosoftTranslator\ApiCall;

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

    /**
     * Translates a given text or html to the given language
     *
     * The language of the given text or html is optional, and will be auto-detected
     * The category will default to "general"
     *
     * @param string $text
     * @param string $to
     * @param string|null $from
     * @param string $contentType
     * @param string|null $category
     * @return string
     */
    public function translate(
        $text,
        $to,
        $from = null,
        $category = null,
        $contentType = ApiCall\Translate::CONTENT_TYPE_TEXT
    )
    {
        $apiCall = new ApiCall\Translate($text, $to, $from, $category, $contentType);

        return $this->call($apiCall);
    }

    /**
     * Translates an array of texts
     *
     * @see MicrosoftTranslator::translate()
     *
     * @param array $texts
     * @param string $to
     * @param string|null $from
     * @return array An array of translated strings
     */
    public function translateArray(array $texts, $to, $from = null)
    {
        $apiCall = new ApiCall\TranslateArray($texts, $to, $from);

        return $this->call($apiCall);
    }

    /**
     * Retrieves an array of translations for a given language pair from the 
     * store and the MT engine. GetTranslations differs from Translate as it 
     * returns all available translations. 
     *
     * The language of the given text is optional, and will be auto-detected.
     * The maximum number of translations defaults to four (4).
     *
     * @param string $text
     * @param string $to
     * @param string|null $from
     * @param int|null $maxTranslations
     * @return \MatthiasNoback\MicrosoftTranslator\ApiCall\Response\TranslationMatch[]
     */
    public function getTranslations($text, $to, $from = null, $maxTranslations = 4)
    {
        $apiCall = new ApiCall\GetTranslations($text, $to, $from, $maxTranslations);

        return $this->call($apiCall);
    }
    
    /**
     * Detects the language of a given text
     *
     * @param string $text
     * @return string The language code
     */
    public function detect($text)
    {
        $apiCall = new ApiCall\Detect($text);

        return $this->call($apiCall);
    }

    /**
     * Detect the languages of multiple texts at once
     *
     * @param array $texts
     * @return array An array of language codes
     */
    public function detectArray(array $texts)
    {
        $apiCall = new ApiCall\DetectArray($texts);

        return $this->call($apiCall);
    }

    /**
     * Break a given text into the sentences it contains
     *
     * @param string $text
     * @param string $language
     * @return array An array of strings
     */
    public function breakSentences($text, $language)
    {
        $apiCall = new ApiCall\BreakSentences($text, $language);

        return $this->call($apiCall);
    }

    /**
     * Get a spoken version of the given text (in WAV or MP3 format)
     *
     * @param $text
     * @param string $language
     * @param string|null $format Either audio/wav or audio/mp3
     * @param string|null $options Either MaxQuality or MinSize
     * @return string Raw data for either an MP3 or a WAV file
     */
    public function speak($text, $language, $format = null, $options = null)
    {
        $apiCall = new ApiCall\Speak($text, $language, $format, $options);

        return $this->call($apiCall);
    }

    /**
     * Get a list of available language codes for the Speak call
     *
     * @see MicrosoftTranslator::speak()
     *
     * @return array An array of language codes
     */
    public function getLanguagesForSpeak()
    {
        $apiCall = new ApiCall\GetLanguagesForSpeak();

        return $this->call($apiCall);
    }

    /**
     * Get a list of available language codes for the Translate calls
     *
     * @see MicrosoftTranslator::translate()
     *
     * @return array An array of language codes
     */
    public function getLanguagesForTranslate()
    {
        $apiCall = new ApiCall\GetLanguagesForTranslate();

        return $this->call($apiCall);
    }

    /**
     * Get a list of language names for the given language codes readable for the given locale
     *
     * @param array $languageCodes
     * @param string $locale
     * @return array An array of language names
     */
    public function getLanguageNames(array $languageCodes, $locale)
    {
        $apiCall = new ApiCall\GetLanguageNames($languageCodes, $locale);

        return $this->call($apiCall);
    }

    /**
     * @param \MatthiasNoback\MicrosoftTranslator\ApiCall\ApiCallInterface $apiCall
     * @return mixed
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

        try {
            $response = $this->browser->call($url, $method, $headers, $content);
        }
        catch (\Exception $previous) {
            throw new RequestFailedException(sprintf(
                'Request failed: %s',
                $previous->getMessage()
            ), null, $previous);
        }

        if (!$response->isSuccessful()) {
            throw new RequestFailedException(sprintf(
                'API call was not successful, %d: %s',
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));
        }

        /* @var $response \Buzz\Message\Response */

        $responseContent = $response->getContent();

        return $apiCall->parseResponse($responseContent);
    }

    private function getAccessToken()
    {
        return $this->accessTokenProvider->getAccessToken(self::ACCESS_TOKEN_SCOPE, self::ACCESS_TOKEN_GRANT_TYPE);
    }
}
