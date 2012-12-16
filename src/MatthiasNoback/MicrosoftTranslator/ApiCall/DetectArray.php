<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

class DetectArray extends AbstractMicrosoftTranslatorApiCall
{
    private $texts;

    public function __construct(array $texts)
    {
        $totalLengthOfTexts = self::calculateTotalLengthOfTexts($texts);
        if ($totalLengthOfTexts > self::MAXIMUM_LENGTH_OF_TEXT) {
            throw new \InvalidArgumentException(sprintf(
                'A maximum amount of %d characters is allowed',
                self::MAXIMUM_LENGTH_OF_TEXT
            ));
        }

        $this->texts = $texts;
    }

    public function getApiMethodName()
    {
        return 'DetectArray';
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

        foreach ($this->texts as $text) {
            $stringElement = $document->createElement('string');
            $stringElement->appendChild($document->createTextNode($text));
            $rootElement->appendChild($stringElement);
        }

        return $document->saveXML();
    }

    public function getQueryParameters()
    {
    }

    public function parseResponse($response)
    {
        $languageCodes = self::getArrayOfStringsFromXml($response);

        return $languageCodes;
    }
}
