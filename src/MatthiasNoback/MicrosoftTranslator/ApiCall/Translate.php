<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

class Translate extends AbstractMicrosoftTranslatorApiCall
{
    private $text;
    private $to;
    private $from;
    private $category;

    public function __construct($text, $to, $from = '', $category = 'general')
    {
        if (strlen($text) > self::MAXIMUM_LENGTH_OF_TEXT) {
            throw new \InvalidArgumentException(sprintf('Text may not be longer than %d characters', self::MAXIMUM_LENGTH_OF_TEXT));
        }

        $this->text = $text;
        $this->to = $to;
        $this->from = $from;
        $this->category = $category;
    }

    public function getApiMethodName()
    {
        return 'Translate';
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
            'text'        => $this->text,
            'from'        => $this->from,
            'to'          => $this->to,
            'category'    => $this->category,
            'contentType' => 'text/plain',
        );
    }

    public function parseResponse($response)
    {
        $simpleXml = $this->toSimpleXML($response);

        return (string) $simpleXml;
    }
}
