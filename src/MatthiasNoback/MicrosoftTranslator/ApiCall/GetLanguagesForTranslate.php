<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

class GetLanguagesForTranslate extends AbstractGetLanguages
{
    public function parseResponse($response)
    {
        $languages = parent::parseResponse($response);
        return array_keys($languages);
    }
}
