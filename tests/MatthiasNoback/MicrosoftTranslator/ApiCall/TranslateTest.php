<?php

namespace MatthiasNoback\Tests\MicrosoftTranslator\ApiCall;

use PHPUnit\Framework\TestCase;
use MatthiasNoback\MicrosoftTranslator\ApiCall;

class TranslateTest extends TestCase
{
    public function testValidatesLengthOfText()
    {
        $text = str_repeat('t', 10001);

        $this->expectException('\InvalidArgumentException');

        new ApiCall\Translate($text, 'nl');
    }

    public function testGetRequestToTranslateCallContent()
    {
        $apiCall = new ApiCall\Translate('text', 'nl');

        $this->assertSame('translate', $apiCall->getApiMethodName());
        $this->assertSame('POST', $apiCall->getHttpMethod());
        $this->assertSame([['Text' => 'text']], $apiCall->getRequestContent());
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
            'from'        => $from,
            'to'          => $to,
            'textType' => $contentType,
            'category'    => $category,
        ), $apiCall->getQueryParameters());
    }

    public function testParseResponse()
    {
        $apiCall = new ApiCall\Translate('text', 'nl');

        $response = json_encode([
            ['translations' => [
                ['text' => 'Dit is een test'],
            ]]
        ]);

        $this->assertSame('Dit is een test', $apiCall->parseResponse($response));
    }
}
