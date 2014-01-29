<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

use MatthiasNoback\Exception\InvalidResponseException;

abstract class AbstractMicrosoftTranslatorApiCall implements ApiCallInterface
{
    const HTTP_API_URL = 'http://api.microsofttranslator.com/V2/Http.svc/';

    const MAXIMUM_LENGTH_OF_TEXT = 10000;

    abstract public function getApiMethodName();

    public function getUrl()
    {
        $url = self::HTTP_API_URL.$this->getApiMethodName();
        if (null !== $queryParameters = $this->getQueryParameters()) {
            $url .= '?'.http_build_query($queryParameters, null, '&');
        }

        return $url;
    }

    protected static function toSimpleXML($xmlString)
    {
        $useInternalErrors = libxml_use_internal_errors(true);

        $simpleXml = new \SimpleXMLElement($xmlString);

        libxml_use_internal_errors($useInternalErrors);

        return $simpleXml;
    }

    protected static function calculateTotalLengthOfTexts(array $texts)
    {
        $totalLength = 0;

        array_walk($texts, function($text) use (&$totalLength) {
            $totalLength += strlen($text);
        });

        return $totalLength;
    }

    protected static function getArrayOfStringsFromXml($xmlString)
    {
        $simpleXml = self::toSimpleXML($xmlString);

        if (!isset($simpleXml->{"string"})) {
            throw new InvalidResponseException('Expected root element of response to contain one or more "string" elements');
        }

        $strings = array();
        foreach ($simpleXml->{"string"} as $string) {
            $strings[] = (string) $string;
        }

        return $strings;
    }
}
