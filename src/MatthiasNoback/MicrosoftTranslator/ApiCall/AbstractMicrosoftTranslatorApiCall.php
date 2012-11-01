<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

abstract class AbstractMicrosoftTranslatorApiCall implements ApiCallInterface
{
    const HTTP_API_URL = 'http://api.microsofttranslator.com/V2/Http.svc/';

    const MAXIMUM_LENGTH_OF_TEXT = 10000;

    abstract protected function getApiMethodName();

    public function getUrl()
    {
        $url = self::HTTP_API_URL.$this->getApiMethodName();
        if (null !== $queryParameters = $this->getQueryParameters()) {
            $url .= '?'.http_build_query($queryParameters);
        }

        return $url;
    }

    protected function toSimpleXML($xmlString)
    {
        $useInternalErrors = libxml_use_internal_errors(true);

        $simpleXml = new \SimpleXMLElement($xmlString);

        libxml_use_internal_errors($useInternalErrors);

        return $simpleXml;
    }
}
