<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

class Detect extends AbstractMicrosoftTranslatorApiCall
{
    const MAXIMUM_LENGTH_OF_TEXT = 50000;

    private $text;

    public function __construct($text)
    {
        if (mb_strlen($text) > self::MAXIMUM_LENGTH_OF_TEXT) {
            throw new \InvalidArgumentException(sprintf('Text may not be longer than %d characters', self::MAXIMUM_LENGTH_OF_TEXT));
        }

        $this->text = $text;
    }

    public function getApiMethodName()
    {
        return 'detect';
    }

    public function getHttpMethod()
    {
        return 'POST';
    }

    public function getRequestContent()
    {
      return array(
        array(
          'Text' => $this->text,
        )
      );
    }

    public function getQueryParameters()
    {
        return [];
    }

    public function parseResponse($response)
    {
        $result = json_decode($response, true);
        return $result[0]['language'];
    }
}
