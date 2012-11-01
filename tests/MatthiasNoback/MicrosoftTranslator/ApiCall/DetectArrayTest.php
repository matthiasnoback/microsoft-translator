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
        $this->assertSame('DetectArray', $apiCall->getApiMethodName());
        $this->assertSame(null, $apiCall->getQueryParameters());
    }

    public function testArrayOfStringsAsRequestContent()
    {
        $texts = array('This is a test', 'Dit is een test');
        $apiCall = new ApiCall\DetectArray($texts);

        $expectedRequestContent = <<<EOF
<?xml version="1.0"?>
<ArrayOfstring xmlns="http://schemas.microsoft.com/2003/10/Serialization/Arrays"><string>This is a test</string><string>Dit is een test</string></ArrayOfstring>

EOF;
;

        $requestContent = $apiCall->getRequestContent();

        $this->assertSame($expectedRequestContent, $requestContent);
    }

    public function testTakesTheLanguageCodesFromTheResponse()
    {
        $response = <<<EOF
<ArrayOfstring xmlns="http://schemas.microsoft.com/2003/10/Serialization/Arrays" xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><string>en</string><string>nl</string></ArrayOfstring>
EOF;

        $texts = array('This is a test', 'Dit is een test');
        $apiCall = new ApiCall\DetectArray($texts);

        $this->assertSame(array('en', 'nl'), $apiCall->parseResponse($response));
    }
}
