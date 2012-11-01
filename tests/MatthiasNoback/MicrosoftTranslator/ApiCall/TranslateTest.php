<?php

namespace MatthiasNoback\Tests\MicrosoftTranslator\ApiCall;

use MatthiasNoback\MicrosoftTranslator\ApiCall;

class TranslateTest extends \PHPUnit_Framework_TestCase
{
    public function testValidatesLengthOfText()
    {
        $text = str_repeat('t', 10001);

        $this->setExpectedException('\InvalidArgumentException');

        new ApiCall\Translate($text, 'nl');
    }

    public function testGetRequestToTranslateMethodWithNoRequestContent()
    {
        $apiCall = new ApiCall\Translate('text', 'nl');

        $this->assertSame('Translate', $apiCall->getApiMethodName());
        $this->assertSame('GET', $apiCall->getHttpMethod());
        $this->assertSame(null, $apiCall->getRequestContent());
    }

    public function testQueryParameters()
    {
        $text = 'text';
        $from = 'from';
        $to = 'to';
        $category = 'category';

        $apiCall = new ApiCall\Translate($text, $to, $from, $category);
        $this->assertEquals(array(
            'text'        => $text,
            'from'        => $from,
            'to'          => $to,
            'category'    => $category,
            'contentType' => 'text/plain',
        ), $apiCall->getQueryParameters());
    }

    public function testParseResponse()
    {
        $apiCall = new ApiCall\Translate('text', 'nl');

        $response = '<string xmlns="http://schemas.microsoft.com/2003/10/Serialization/">Dit is een test</string>';

        $this->assertSame('Dit is een test', $apiCall->parseResponse($response));
    }
}
