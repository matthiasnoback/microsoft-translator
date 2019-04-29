<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

class DetectArray extends AbstractMicrosoftTranslatorApiCall
{
    private $texts;

    public function __construct(array $texts)
    {
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
        $document = new \DOMDocument();

        $rootElement = $document->createElementNS('http://schemas.microsoft.com/2003/10/Serialization/Arrays', 'ArrayOfstring');
        $document->appendChild($rootElement);

        $content = [];
        foreach ($this->texts as $text) {
            $content[] = ['Text' => $text];
        }

        return $content;
    }

    public function getQueryParameters()
    {
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
