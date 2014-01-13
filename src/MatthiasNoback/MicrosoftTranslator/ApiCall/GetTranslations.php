<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

class GetTranslations extends AbstractMicrosoftTranslatorApiCall
{
    private $text;
    private $to;
    private $from;
    private $maxTranslations;
    private $category;

    public function __construct($text, $to, $from = null, $maxTranslations = 4, $category = null)
    {
        if (strlen($text) > self::MAXIMUM_LENGTH_OF_TEXT) {
            throw new \InvalidArgumentException(sprintf('Text may not be longer than %d characters', self::MAXIMUM_LENGTH_OF_TEXT));
        }

        $this->text = $text;
        $this->to = $to;
        $this->from = $from;
        $this->maxTranslations = $maxTranslations;
        $this->category = $category;
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
        /*
        <TranslateOptions xmlns="http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2">
          <Category>string-value</Category>
          <ContentType>text/plain</ContentType>
          <ReservedFlags></ReservedFlags>
          <State>int-value</State>
          <Uri>string-value</Uri>
          <User>string-value</User>
        </TranslateOptions>
        */
    }

    public function getQueryParameters()
    {
        return array(
            'text'        => $this->text,
            'from'        => $this->from,
            'to'          => $this->to,
            'maxTranslations' => $this->maxTranslations,
            'options' => array(
              'Category'    => $this->category,
              'ContentType' => 'text/plain'
            )
        );
    }

    public function parseResponse($response)
    {
        $simpleXml = $this->toSimpleXML($response);

        $translations = array();

        if (!isset($simpleXml->{"GetTranslationsResponse"})) {
            throw new InvalidResponseException('Expected root element of the response to contain one or more "GetTranslationsResponse" elements');
        }

        foreach ($simpleXml->{"GetTranslationsResponse"} as $getTranslationsResponse) {
            if (isset($getTranslationsResponse->Error) && $getTranslationsResponse->Error) {
                // TODO maybe find a better way to handle translation errors
            }
            else {
                if (!isset($getTranslationsResponse->Translations)) {
                    throw new InvalidResponseException('Expected root element of the response to contain a "Translations" element');
                }
                else if (!isset($getTranslationsResponse->Translations->TranslationMatch)) {
                    throw new InvalidResponseException('Expected "Translations" element of the response to contain a "TranslationMatch" element');
                }
                $matches = $getTranslationsResponse->Translations->TranslationMatch;

                foreach ($matches as $translationMatch) {
                    // MatchDegree and Rating might be interesting...
                    $translations[] = (string) $translationMatch->TranslatedText;
                }
            }
        }

        return $translations;
    }
}
