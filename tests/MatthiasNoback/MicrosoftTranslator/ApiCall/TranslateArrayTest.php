<?php

namespace MatthiasNoback\Tests\MicrosoftTranslator\ApiCall;

use PHPUnit\Framework\TestCase;
use MatthiasNoback\MicrosoftTranslator\ApiCall;

class TranslateArrayTest extends TestCase
{
    public function testPostRequestMethod()
    {
        $apiCall = new ApiCall\TranslateArray(array('text'), 'nl');

        $this->assertSame('translate', $apiCall->getApiMethodName());
        $this->assertSame('POST', $apiCall->getHttpMethod());
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

    public function testValidatesNumberOfTexts()
    {
        $texts = range(1, 1001);
        $this->expectException('\InvalidArgumentException');

        new ApiCall\TranslateArray($texts, 'nl');
    }

    public function testValidatesTotalLengthOfTexts()
    {
        $texts = array();
        for ($i = 0; $i <= 50; $i++) {
            $texts[] = str_repeat('t', 1000);
        }

        $this->expectException('\InvalidArgumentException');

        new ApiCall\TranslateArray($texts, 'nl');
    }

    public function testGetRequestContent()
    {
        $texts = array(
            'This is a test',
            'My name is Matthias',
        );
        $to = 'nl';
        $from = 'en';

        $apiCall = new ApiCall\TranslateArray($texts, $to, $from);

        $requestContent = $apiCall->getRequestContent();

        $expectedRequestContent = [
            [
                'Text' => $texts[0]
            ],
            [
                'Text' => $texts[1]
            ]
        ];
        $this->assertSame(json_encode($expectedRequestContent), json_encode($requestContent));
    }

    public function testParseResponse()
    {
        $texts = array(
            'This is a test',
            'My name is Matthias'
        );

        $response = json_encode([
            ['translations' => [
                ['text' => 'Dit is een test'],
            ]],
            ['translations' => [
                ['text' => 'Mijn naam is Matthias']
            ]]
        ]);

        $to = 'nl';
        $from = 'en';
        $apiCall = new ApiCall\TranslateArray($texts, $to, $from);
        $translatedTexts = $apiCall->parseResponse($response);
        $this->assertSame(json_encode(array(
            'Dit is een test',
            'Mijn naam is Matthias',
        )), json_encode($translatedTexts));
    }
}
