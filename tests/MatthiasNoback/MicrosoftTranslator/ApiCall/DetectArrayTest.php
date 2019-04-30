<?php

namespace MatthiasNoback\Tests\MicrosoftTranslator\ApiCall;

use MatthiasNoback\MicrosoftTranslator\ApiCall;

class DetectArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testPostCallToApiMethodDetectArrayWithNoQueryParameters()
    {
        $texts = array('This is a test', 'Dit is een test');
        $apiCall = new ApiCall\DetectArray($texts);

        $this->assertSame('POST', $apiCall->getHttpMethod());
        $this->assertSame('detect', $apiCall->getApiMethodName());
        $this->assertSame(null, $apiCall->getQueryParameters());
    }

    public function testArrayOfStringsAsRequestContent()
    {
        $texts = array('This is a test', 'Dit is een test');
        $apiCall = new ApiCall\DetectArray($texts);

        $expectedRequestContent = json_encode([['Text' => 'This is a test'], ['Text' => 'Dit is een test']]);

        $requestContent = json_encode($apiCall->getRequestContent());
        $this->assertSame($expectedRequestContent, $requestContent);
    }

    public function testTakesTheLanguageCodesFromTheResponse()
    {
        $response = json_encode([
            ['language' => 'en'],
            ['language' => 'nl']
        ]);

        $texts = array('This is a test', 'Dit is een test');
        $apiCall = new ApiCall\DetectArray($texts);

        $this->assertSame(array('en', 'nl'), $apiCall->parseResponse($response));
    }
}
