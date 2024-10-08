<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

abstract class AbstractMicrosoftTranslatorApiCall implements ApiCallInterface
{
    const HTTP_API_URL = 'https://api.cognitive.microsofttranslator.com/';

    abstract public function getApiMethodName();

    public function getUrl()
    {
        $url = self::HTTP_API_URL.$this->getApiMethodName();
        if (null !== $queryParameters = $this->getQueryParameters()) {
            $queryParameters['api-version'] = '3.0';
            $url .= '?'.http_build_query($queryParameters, '', '&');
        }
        return $url;
    }

    protected static function calculateTotalLengthOfTexts(array $texts)
    {
        $totalLength = 0;

        array_walk($texts, function($text) use (&$totalLength) {
            $totalLength += mb_strlen($text);
        });

        return $totalLength;
    }

    public function getRequestHeaders() {
        return [];
    }

    public function sendHeaders()
    {
        return true;
    }
}
