<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

abstract class AbstractGetLanguages extends AbstractMicrosoftTranslatorApiCall
{
    public function getHttpMethod()
    {
        return 'GET';
    }

    public function getRequestContent()
    {
    }

    public function getQueryParameters()
    {
    }

    public function parseResponse($response)
    {
        $languageCodes = self::getArrayOfStringsFromXml($response);

        return $languageCodes;
    }
}
