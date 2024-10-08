<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

class DetectArray extends AbstractMicrosoftTranslatorApiCall
{
    const MAXIMUM_LENGTH_OF_TEXT = 50000;
    const MAXIMUM_NUMBER_OF_ARRAY_ELEMENTS = 100;
    private $texts;

    public function __construct(array $texts)
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
        $content = [];
        foreach ($this->texts as $text) {
            $content[] = ['Text' => $text];
        }

        return $content;
    }

    public function getQueryParameters()
    {
        return [];
    }

    public function parseResponse($response)
    {
      $result = [];
      $texts = json_decode($response, true);
      foreach ($texts as $text) {
        $result[] = $text['language'];
      }
      return $result;
    }
}
