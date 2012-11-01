<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

interface ApiCallInterface
{
    public function getUrl();
    public function getHttpMethod();
    public function getRequestContent();
    public function getQueryParameters();
    public function parseResponse($response);
}
