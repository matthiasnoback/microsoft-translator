<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

use MatthiasNoback\Exception\InvalidResponseException;

class TranslateArray extends Translate
{
    const MAXIMUM_NUMBER_OF_ARRAY_ELEMENTS = 2000;

    public function __construct($text, $to, $from = null, $category = null, $contentType = self::CONTENT_TYPE_TEXT)
    {
        if (count($text) > self::MAXIMUM_NUMBER_OF_ARRAY_ELEMENTS) {
            throw new \InvalidArgumentException(sprintf(
                'A maximum amount of %d texts is allowed',
                self::MAXIMUM_NUMBER_OF_ARRAY_ELEMENTS
            ));
        }

        $totalLengthOfTexts = self::calculateTotalLengthOfTexts($text);
        if ($totalLengthOfTexts > self::MAXIMUM_LENGTH_OF_TEXT) {
            throw new \InvalidArgumentException(sprintf(
                'A maximum amount of %d characters is allowed',
                self::MAXIMUM_LENGTH_OF_TEXT
            ));
        }

        $this->text = $text;
        $this->to = $to;
        $this->from = $from;
        $this->contentType = $contentType;
        $this->category = $category;
    }

    public function getRequestContent()
    {
        $content = array();
        foreach ($this->text as $text) {
            $content[] = [
                'Text' =>  $text
            ];
        }
        return $content;
    }

    public function parseResponse($response)
    {
        $result = [];
        $texts = json_decode($response, true);
        foreach ($texts as $text) {
          $result[] = $text['translations'][0]['text'];
        }
        return $result;
    }
}
