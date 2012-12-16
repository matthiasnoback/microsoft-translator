<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

class GetLanguageNames extends AbstractMicrosoftTranslatorApiCall
{
    private $languageCodes;
    private $locale;

    public function __construct(array $languageCodes, $locale)
    {
        $this->languageCodes = $languageCodes;
        $this->locale = $locale;
    }

    public function getApiMethodName()
    {
        return 'GetLanguageNames';
    }

    public function getHttpMethod()
    {
        return 'POST';
    }

    public function getRequestContent()
    {
        $document = new \DOMDocument();

        $rootElement = $document->createElementNS('http://schemas.microsoft.com/2003/10/Serialization/Arrays', 'ArrayOfstring');
        $document->appendChild($rootElement);

        foreach ($this->languageCodes as $languageCode) {
            $stringElement = $document->createElement('string');
            $stringElement->appendChild($document->createTextNode($languageCode));
            $rootElement->appendChild($stringElement);
        }

        return $document->saveXML();
    }

    public function getQueryParameters()
    {
        return array(
            'locale' => $this->locale,
        );
    }

    public function parseResponse($response)
    {
        $languageNames = self::getArrayOfStringsFromXml($response);

        return array_combine($this->languageCodes, $languageNames);
    }
}
