<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

class Translate extends AbstractMicrosoftTranslatorApiCall
{
    const CONTENT_TYPE_TEXT = 'plain';
    const CONTENT_TYPE_HTML = 'html';

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
        return 'translate';
    }

    public function getHttpMethod()
    {
        return 'POST';
    }

    public function getRequestContent()
    {
        return array(
            array(
                'Text' => $this->text
            )
        );
    }

    public function getQueryParameters()
    {
        return array(
            'from'        => $this->from,
            'to'          => $this->to,
            'textType' => $this->contentType,
            'category'    => $this->category,
        );
    }

    public function parseResponse($response)
    {
        $response = json_decode($response, true);
        return $response[0]['translations'][0]['text'];
    }
}
