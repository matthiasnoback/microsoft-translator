<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

interface ApiCallInterface
{
    /**
     * The full URL, including query parameters to be used
     *
     * @abstract
     * @return string
     */
    public function getUrl();

    /**
     * The HTTP method (GET, POST, etc.) to be used
     *
     * @abstract
     * @return string
     */
    public function getHttpMethod();

    /**
     * The content of the request to be sent, or null
     *
     * @abstract
     * @return string|array|null
     */
    public function getRequestContent();

    /**
     * An array of query parameters to be used, or null
     *
     * @abstract
     * @return array|null
     */
    public function getQueryParameters();

    public function getRequestHeaders();

    /**
     * Transform the response into something useful
     *
     * @abstract
     * @param string $response
     * @return mixed
     */
    public function parseResponse($response);

    public function sendHeaders();
}
