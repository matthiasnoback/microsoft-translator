<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

class BreakSentences extends AbstractMicrosoftTranslatorApiCall
{
    const MAXIMUM_LENGTH_OF_TEXT = 50000;

    private $text;
    private $language;

    public function __construct($text, $language)
    {
        if (mb_strlen($text) > self::MAXIMUM_LENGTH_OF_TEXT) {
            throw new \InvalidArgumentException(sprintf('Text may not be longer than %d characters', self::MAXIMUM_LENGTH_OF_TEXT));
        }

        $this->text = $text;
        $this->language = $language;
    }

    public function getApiMethodName()
    {
        return 'breaksentence';
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
            'language' => $this->language,
        );
    }

    public function parseResponse($response)
    {
        $boundaries = json_decode($response, true);
        $sentences = [];
        $init = 0;
        foreach ($boundaries[0]['sentLen'] as $offset) {
            $sentences[] = mb_substr($this->text, $init, $offset);
            $init += $offset;
        }
        return $sentences;
    }
}
