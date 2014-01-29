<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

use MatthiasNoback\Exception\InvalidResponseException;
use MatthiasNoback\MicrosoftTranslator\ApiCall\Response\TranslationMatch;

class GetTranslations extends AbstractMicrosoftTranslatorApiCall
{
    private $text;
    private $to;
    private $from;
    private $maxTranslations;

    public function __construct($text, $to, $from = null, $maxTranslations = 4)
    {
        if (strlen($text) > self::MAXIMUM_LENGTH_OF_TEXT) {
            throw new \InvalidArgumentException(sprintf(
                'Text may not be longer than %d characters',
                self::MAXIMUM_LENGTH_OF_TEXT
            ));
        }

        $this->text = $text;
        $this->to = $to;
        $this->from = $from;
        $this->maxTranslations = $maxTranslations;
    }

    public function getApiMethodName()
    {
        return 'GetTranslations';
    }

    public function getHttpMethod()
    {
        return 'POST';
    }

    public function getRequestContent()
    {
        return null;
    }

    public function getQueryParameters()
    {
        return array(
            'text' => $this->text,
            'from' => $this->from,
            'to' => $this->to,
            'maxTranslations' => $this->maxTranslations
        );
    }

    public function parseResponse($response)
    {
        $simpleXml = $this->toSimpleXML($response);

        $translations = array();

        if ($simpleXml->getName() !== 'GetTranslationsResponse') {
            throw new InvalidResponseException('Expected root element to be a "GetTranslationsResponse" element');
        }

        if (!isset($simpleXml->Translations)) {
            throw new InvalidResponseException('Expected root element of the response to contain a "Translations" element');
        }

        foreach ($simpleXml->Translations as $getTranslationsResponse) {
            if (isset($getTranslationsResponse->Error) && $getTranslationsResponse->Error) {
                continue;
            } else {
                if (!isset($getTranslationsResponse->TranslationMatch)) {
                    throw new InvalidResponseException('Expected "Translations" element of the response to contain a "TranslationMatch" element');
                }
                $matches = $getTranslationsResponse->TranslationMatch;

                foreach ($matches as $translationMatch) {
                    if (!isset($translationMatch->TranslatedText)) {
                        throw new InvalidResponseException('Expected "TranslationMatch" element to contain a "TranslatedText" element');
                    }
                    if (!isset($translationMatch->MatchDegree)) {
                        throw new InvalidResponseException('Expected "TranslationMatch" element to contain a "MatchDegree" element');
                    }
                    $match = new TranslationMatch(
                        (string) $translationMatch->TranslatedText,
                        (integer) $translationMatch->MatchDegree
                    );
                    $translations[] = $match;
                }
            }
        }

        return $translations;
    }
}
