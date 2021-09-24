<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

abstract class AbstractGetLanguages extends AbstractMicrosoftTranslatorApiCall
{
    public function getHttpMethod()
    {
        return 'GET';
    }

    public function getApiMethodName()
    {
        return 'languages';
    }

    public function getRequestContent()
    {
        return null;
    }

    public function getQueryParameters()
    {
        return  [
            'scope' => 'translation'
        ];
    }

    public function parseResponse($response)
    {
        $result = json_decode($response, true);
        return $result['translation'];
    }

    public function getRequestHeaders() {
        return [];
    }

    public function sendHeaders()
    {
        return false;
    }
}
