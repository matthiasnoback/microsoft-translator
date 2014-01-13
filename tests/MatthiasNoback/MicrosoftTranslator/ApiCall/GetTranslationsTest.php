<?php

namespace MatthiasNoback\Tests\MicrosoftTranslator\ApiCall;

use MatthiasNoback\MicrosoftTranslator\ApiCall;

class GetTranslationsTest extends \PHPUnit_Framework_TestCase
{
    public function testValidatesLengthOfText()
    {
        $text = str_repeat('t', 10001);

        $this->setExpectedException('\InvalidArgumentException');

        new ApiCall\GetTranslations($text, 'nl');
    }

    public function testGetRequestToTranslateMethodWithNoRequestContent()
    {
        $apiCall = new ApiCall\GetTranslations('text', 'nl');

        $this->assertSame('GetTranslations', $apiCall->getApiMethodName());
        $this->assertSame('POST', $apiCall->getHttpMethod());
        $this->assertSame(null, $apiCall->getRequestContent());
    }

    public function testQueryParameters()
    {
        $text = 'text';
        $from = 'from';
        $to = 'to';
        $maxTranslations = 4;
        $category = 'category';

        $apiCall = new ApiCall\GetTranslations($text, $to, $from, $maxTranslations, $category);
        $this->assertEquals(array(
            'text'        => $text,
            'from'        => $from,
            'to'          => $to,
            'maxTranslations' => $maxTranslations,
            'options'     => array(
              'Category'    => $category,
              'ContentType' => 'text/plain',
            )
        ), $apiCall->getQueryParameters());
    }

    public function testParseResponse()
    {
        $apiCall = new ApiCall\GetTranslations('text', 'nl');

        $response = '<string xmlns="http://schemas.microsoft.com/2003/10/Serialization/">Dit is een test</string>';

        $this->assertSame('Dit is een test', $apiCall->parseResponse($response));
    }
}
