<?php

namespace MatthiasNoback\Tests\MicrosoftTranslator\ApiCall;

use MatthiasNoback\MicrosoftTranslator\ApiCall;

class DetectTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCallToApiMethodDetectWithNoRequestContentAndOnlyTextAsParameter()
    {
        $text = 'This is a test';
        $apiCall = new ApiCall\Detect($text);

        $this->assertSame('GET', $apiCall->getHttpMethod());
        $this->assertSame(null, $apiCall->getRequestContent());
        $this->assertSame('Detect', $apiCall->getApiMethodName());
        $this->assertSame(array('text' => $text), $apiCall->getQueryParameters());
    }

    public function testTakesTheLanguageCodeFromTheResponse()
    {
        $response = <<<EOF
<string xmlns="http://schemas.microsoft.com/2003/10/Serialization/">en</string>
EOF;

        $apiCall = new ApiCall\Detect('This is a test');

        $this->assertSame('en', $apiCall->parseResponse($response));
    }
}
