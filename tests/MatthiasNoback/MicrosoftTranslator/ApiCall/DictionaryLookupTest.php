<?php

namespace MatthiasNoback\Tests\MicrosoftTranslator\ApiCall;

use PHPUnit\Framework\TestCase;
use MatthiasNoback\MicrosoftTranslator\ApiCall;

class DictionaryLookupTest extends TestCase
{
    public function testValidatesLengthOfText()
    {
        $text = str_repeat('t', 10001);

        $this->expectException('\InvalidArgumentException');

        new ApiCall\DictionaryLookup($text, 'nl');
    }

    public function testGetRequestToDictionaryLookupCallContent()
    {
        $apiCall = new ApiCall\DictionaryLookup('text', 'nl');

        $this->assertSame('dictionary/lookup', $apiCall->getApiMethodName());
        $this->assertSame('POST', $apiCall->getHttpMethod());
        $this->assertSame([['Text' => 'text']], $apiCall->getRequestContent());
    }

    public function testQueryParameters()
    {
        $text = 'text';
        $from = 'from';
        $to = 'to';

        $apiCall = new ApiCall\DictionaryLookup($text, $to, $from);
        $this->assertEquals(array(
            'from'        => $from,
            'to'          => $to
        ), $apiCall->getQueryParameters());
    }

    public function testParseResponse()
    {
        $apiCall = new ApiCall\DictionaryLookup('peter', 'eu');

        $response = json_encode([
            ['translations' => [
                ['text' => 'Dit is een test'],
            ]]
        ]);

        $this->assertSame([['text' => 'Dit is een test']], $apiCall->parseResponse($response));
    }
}
