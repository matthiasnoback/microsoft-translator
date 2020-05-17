<?php

namespace MatthiasNoback\Tests\MicrosoftTranslator\ApiCall;

use PHPUnit\Framework\TestCase;
use MatthiasNoback\MicrosoftTranslator\ApiCall;

class DetectTest extends TestCase
{
    public function testGetCallToApiMethodDetectWithNoQueryParameters()
    {
        $text = 'This is a test';
        $apiCall = new ApiCall\Detect($text);

        $this->assertSame('POST', $apiCall->getHttpMethod());
        $this->assertSame('detect', $apiCall->getApiMethodName());
        $this->assertSame([], $apiCall->getQueryParameters());
    }

    public function testArrayOfTextAsRequestContent()
    {
        $text = 'This is a test';
        $apiCall = new ApiCall\Detect($text);

        $expectedRequestContent = json_encode([['Text' => $text]]);
        $requestContent = json_encode($apiCall->getRequestContent());
        $this->assertSame($expectedRequestContent, $requestContent);
    }

    public function testTakesTheLanguageCodeFromTheResponse()
    {
        $response = json_encode([['language' => 'en']]);

        $apiCall = new ApiCall\Detect('This is a test');

        $this->assertSame('en', $apiCall->parseResponse($response));
    }
}
