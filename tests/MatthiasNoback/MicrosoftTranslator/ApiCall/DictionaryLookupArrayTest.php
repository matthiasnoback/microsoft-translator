<?php

namespace MatthiasNoback\Tests\MicrosoftTranslator\ApiCall;

use PHPUnit\Framework\TestCase;
use MatthiasNoback\MicrosoftTranslator\ApiCall;

class DictionaryLookupArrayTest extends TestCase
{
    public function testValidatesNumberOfTexts()
    {
        $from = 'from';
        $to = 'to';        
        $texts = range(1, 11);
        $this->expectException('\InvalidArgumentException');

        new ApiCall\DictionaryLookupArray($texts, $from, $to);
    }

    public function testValidatesTotalLengthOfTexts()
    {
        $from = 'from';
        $to = 'to';        
        $texts = array();
        for ($i = 0; $i <= 10; $i++) {
            $texts[] = str_repeat('t', 100);
        }

        $this->expectException('\InvalidArgumentException');

        new ApiCall\DictionaryLookupArray($texts, $from, $to);
    }

    public function testGetRequestContent()
    {
        $texts = array(
            'fly',
            'dragonfly',
        );
        $to = 'es';
        $from = 'en';

        $apiCall = new ApiCall\DictionaryLookupArray($texts, $to, $from);

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

    public function testQueryParameters()
    {
        $texts = array(
            'fly',
            'dragonfly',
        );
        $to = 'es';
        $from = 'en';

        $apiCall = new ApiCall\DictionaryLookupArray($texts, $to, $from);

        $queryParameters = $apiCall->getQueryParameters();
        $expectedRequestContent = [
            'from' => $from,
            'to' => $to
        ];
        $this->assertSame(json_encode($expectedRequestContent), json_encode($queryParameters));
    }

    public function testParseResponse()
    {
        $texts = array(
            'fly',
            'dragonfly'
        );

        $to = 'es';
        $from = 'en';
        $apiCall = new ApiCall\DictionaryLookupArray($texts, $to, $from);

        $response = json_encode([
            ['translations' => [
                ['text' => 'Dit is een test'],
            ]],
            ['translations' => [
                ['text' => 'Mijn naam is Matthias']
            ]]
        ]);

        $this->assertSame(json_encode([[['text' => 'Dit is een test']],  [['text' => 'Mijn naam is Matthias']]]), json_encode($apiCall->parseResponse($response)));
    }


}
