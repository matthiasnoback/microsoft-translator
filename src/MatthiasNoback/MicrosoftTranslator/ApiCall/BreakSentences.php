<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

use MatthiasNoback\Exception\InvalidResponseException;

class BreakSentences extends AbstractMicrosoftTranslatorApiCall
{
    private $text;
    private $language;

    public function __construct($text, $language)
    {
        if (strlen($text) > self::MAXIMUM_LENGTH_OF_TEXT) {
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
        //
        // $simpleXml = $this->toSimpleXML($response);
        //
        // $start = 0;
        //
        // $sentences = array();
        //
        // if (!isset($simpleXml->{"int"})) {
        //     throw new InvalidResponseException('Expected the root element of the response to contain one or more "int" elements');
        // }
        //
        // foreach ($simpleXml->{"int"} as $sentenceLength) {
        //     $sentenceLength = (integer) $sentenceLength;
        //
        //     $sentence = substr($this->text, $start, $sentenceLength);
        //     $sentences[] = $sentence;
        //     $start += $sentenceLength;
        // }
        //
        // return $sentences;
    }
}
