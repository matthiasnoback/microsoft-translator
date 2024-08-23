<?php

namespace MatthiasNoback\Tests\MicrosoftTranslator\ApiCall;

use PHPUnit\Framework\TestCase;
use MatthiasNoback\MicrosoftTranslator\ApiCall;
use MatthiasNoback\MicrosoftTranslator\ApiCall\ApiCallInterface;

class DetectArrayTest extends TestCase
{
    public function testPostCallToApiMethodDetectArrayWithNoQueryParameters()
    {
        $texts = array('This is a test', 'Dit is een test');
        $apiCall = new ApiCall\DetectArray($texts);

        $this->assertSame('POST', $apiCall->getHttpMethod());
        $this->assertSame('detect', $apiCall->getApiMethodName());
        $this->assertSame([], $apiCall->getQueryParameters());
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

    public function testValidatesNumberOfTexts()
    {
        $texts = range(1, 101);
        $this->expectException('\InvalidArgumentException');

        new ApiCall\DetectArray($texts, 'nl');
    }

    public function testValidatesTotalLengthOfTexts()
    {
        $texts = array();
        for ($i = 0; $i <= 50; $i++) {
            $texts[] = str_repeat('t', 1000);
        }

        $this->expectException('\InvalidArgumentException');

        new ApiCall\DetectArray($texts, 'nl');
    }
}
