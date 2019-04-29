<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

use MatthiasNoback\Exception\InvalidResponseException;

class TranslateArray extends AbstractMicrosoftTranslatorApiCall
{
    const MAXIMUM_NUMBER_OF_ARRAY_ELEMENTS = 2000;

    private $texts;
    private $to;
    private $from;

    public function __construct(array $texts, $to, $from = null)
    {
        if (count($texts) > self::MAXIMUM_NUMBER_OF_ARRAY_ELEMENTS) {
            throw new \InvalidArgumentException(sprintf(
                'A maximum amount of %d texts is allowed',
                self::MAXIMUM_NUMBER_OF_ARRAY_ELEMENTS
            ));
        }

        $totalLengthOfTexts = self::calculateTotalLengthOfTexts($texts);
        if ($totalLengthOfTexts > self::MAXIMUM_LENGTH_OF_TEXT) {
            throw new \InvalidArgumentException(sprintf(
                'A maximum amount of %d characters is allowed',
                self::MAXIMUM_LENGTH_OF_TEXT
            ));
        }

        $this->texts = $texts;
        $this->to = $to;
        $this->from = $from;
    }

    public function getApiMethodName()
    {
        return 'translate';
    }

    public function getHttpMethod()
    {
        return 'POST';
    }

    public function getRequestContent()
    {
        $content = array();
        foreach ($this->texts as $text) {
            $content[] = [
                'Text' =>  $text
            ];
        }
        return $content;
    }

    public function getQueryParameters()
    {
    }

    public function parseResponse($response)
    {
        return json_decode($response);
        $simpleXml = $this->toSimpleXML($response);

        $translations = array();

        if (!isset($simpleXml->{"TranslateArrayResponse"})) {
            throw new InvalidResponseException('Expected root element of the response to contain one or more "TranslateArrayResponse" elements');
        }

        foreach ($simpleXml->{"TranslateArrayResponse"} as $translateArrayResponse) {
            if (isset($translateArrayResponse->Error) && $translateArrayResponse->Error) {
                $translation = '';
            }
            else {
                if (!isset($translateArrayResponse->TranslatedText)) {
                    throw new InvalidResponseException('Expected root element of the response to contain a "TranslatedText" element');
                }

                $translation = (string) $translateArrayResponse->TranslatedText;
            }

            $translations[] = $translation;
        }

        return $translations;
    }
}
