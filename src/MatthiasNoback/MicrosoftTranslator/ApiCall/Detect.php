<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

class Detect extends AbstractMicrosoftTranslatorApiCall
{
    private $text;

    public function __construct($text)
    {
        if (strlen($text) > self::MAXIMUM_LENGTH_OF_TEXT) {
            throw new \InvalidArgumentException(sprintf('Text may not be longer than %d characters', self::MAXIMUM_LENGTH_OF_TEXT));
        }

        $this->text = $text;
    }

    public function getApiMethodName()
    {
        return 'Detect';
    }

    public function getHttpMethod()
    {
        return 'GET';
    }

    public function getRequestContent()
    {
    }

    public function getQueryParameters()
    {
        return array(
            'text' => $this->text,
        );
    }

    public function parseResponse($response)
    {
        $simpleXml = $this->toSimpleXML($response);

        return (string) $simpleXml;
    }
}
