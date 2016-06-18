<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

class Translate extends AbstractMicrosoftTranslatorApiCall
{
    const CONTENT_TYPE_TEXT = 'text/plain';
    const CONTENT_TYPE_HTML = 'text/html';

    private $text;
    private $to;
    private $from;
    private $contentType;
    private $category;

    public function __construct($text, $to, $from = null, $category = null, $contentType = self::CONTENT_TYPE_TEXT)
    {
        if (strlen($text) > self::MAXIMUM_LENGTH_OF_TEXT) {
            throw new \InvalidArgumentException(sprintf('Text may not be longer than %d characters', self::MAXIMUM_LENGTH_OF_TEXT));
        }

        $this->text = $text;
        $this->to = $to;
        $this->from = $from;
        $this->contentType = $contentType;
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
            'contentType' => $this->contentType,
            'category'    => $this->category,
        );
    }

    public function parseResponse($response)
    {
        $simpleXml = $this->toSimpleXML($response);

        return (string) $simpleXml;
    }
}
