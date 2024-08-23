<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

class DictionaryLookupArray extends AbstractMicrosoftTranslatorApiCall
{
    const MAXIMUM_NUMBER_OF_ARRAY_ELEMENTS = 10;
    const MAXIMUM_LENGTH_OF_TEXT = 100;

    const CONTENT_TYPE_TEXT = 'plain';
    const CONTENT_TYPE_HTML = 'html';
    
    private $texts;
    protected $to;
    protected $from;

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
        return 'dictionary/lookup';
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
        return array(
            'from'        => $this->from,
            'to'          => $this->to,
        );
    }

    public function parseResponse($response)
    {
      $result = [];
      $texts = json_decode($response, true);
      foreach ($texts as $text) {
        $result[] = $text['translations'];
      }

      return $result;
    }
}
