<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

class DictionaryLookup extends AbstractMicrosoftTranslatorApiCall
{
    const CONTENT_TYPE_TEXT = 'plain';
    const CONTENT_TYPE_HTML = 'html';

    protected $text;
    protected $to;
    protected $from;

    public function __construct($text, $to, $from = null)
    {
        if (strlen($text) > self::MAXIMUM_LENGTH_OF_TEXT) {
            throw new \InvalidArgumentException(sprintf('Text may not be longer than %d characters', self::MAXIMUM_LENGTH_OF_TEXT));
        }

        $this->text = $text;
        $this->to = $to;
        $this->from = $from;
    }

    public function getApiMethodName()
    {
        return '/dictionary/lookup';
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
        );
    }

    public function parseResponse($response)
    {
        $response = json_decode($response, true);
        return $response[0]['translations'];
    }
}
