<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

class GetLanguageNames extends AbstractGetLanguages
{
    private $languageCodes;
    private $locale;

    public function __construct(array $languageCodes, $locale)
    {
        $this->languageCodes = $languageCodes;
        $this->locale = $locale;
    }

    public function getRequestHeaders()
    {
        return [
            'Accept-Language: ' . $this->locale
        ];
    }

    public function parseResponse($response)
    {
        return $response;
        $languageNames = self::getArrayOfStringsFromXml($response);

        return array_combine($this->languageCodes, $languageNames);
    }
}
