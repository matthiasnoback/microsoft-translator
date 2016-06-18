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
        $contentType = 'contentType';
        $category = 'category';

        $apiCall = new ApiCall\Translate($text, $to, $from, $category, $contentType);
        $this->assertEquals(array(
            'text'        => $text,
            'from'        => $from,
            'to'          => $to,
            'contentType' => $contentType,
            'category'    => $category,
        ), $apiCall->getQueryParameters());
    }

    public function testParseResponse()
    {
        $apiCall = new ApiCall\Translate('text', 'nl');

        $response = '<string xmlns="http://schemas.microsoft.com/2003/10/Serialization/">Dit is een test</string>';

        $this->assertSame('Dit is een test', $apiCall->parseResponse($response));
    }
}
