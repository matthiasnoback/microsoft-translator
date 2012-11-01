<?php

namespace MatthiasNoback\Tests\MicrosoftTranslator\ApiCall;

use MatthiasNoback\MicrosoftTranslator\ApiCall;

class TranslateArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testPostRequestToTranslateArrayMethodWithNoQueryParameters()
    {
        $apiCall = new ApiCall\TranslateArray(array('text'), 'nl');

        $this->assertSame('TranslateArray', $apiCall->getApiMethodName());
        $this->assertSame('POST', $apiCall->getHttpMethod());
        $this->assertSame(null, $apiCall->getQueryParameters());
    }

    public function testValidatesNumberOfOfTexts()
    {
        $texts = range(1, 2001);

        $this->setExpectedException('\InvalidArgumentException');

        new ApiCall\TranslateArray($texts, 'nl');
    }

    public function testValidatesTotalLengthOfTexts()
    {
        $texts = array();
        for ($i = 0; $i <= 10; $i++) {
            $texts[] = str_repeat('t', 1000);
        }

        $this->setExpectedException('\InvalidArgumentException');

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

        $expectedRequestContent =
<<<EOF
<?xml version="1.0"?>
<TranslateArrayRequest><AppId/><From>$from</From><Texts><string xmlns="http://schemas.microsoft.com/2003/10/Serialization/Arrays">{$texts[0]}</string><string xmlns="http://schemas.microsoft.com/2003/10/Serialization/Arrays">{$texts[1]}</string></Texts><To>{$to}</To></TranslateArrayRequest>

EOF;
        $this->assertSame($expectedRequestContent, $requestContent);
    }

    public function testParseResponse()
    {
        $response = <<<EOF
<ArrayOfTranslateArrayResponse xmlns="http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2" xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><TranslateArrayResponse><From>en</From><OriginalTextSentenceLengths xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"><a:int>14</a:int></OriginalTextSentenceLengths><TranslatedText>Dit is een test</TranslatedText><TranslatedTextSentenceLengths xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"><a:int>15</a:int></TranslatedTextSentenceLengths></TranslateArrayResponse><TranslateArrayResponse><From>en</From><OriginalTextSentenceLengths xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"><a:int>19</a:int></OriginalTextSentenceLengths><TranslatedText>Mijn naam is Matthias</TranslatedText><TranslatedTextSentenceLengths xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"><a:int>21</a:int></TranslatedTextSentenceLengths></TranslateArrayResponse></ArrayOfTranslateArrayResponse>
EOF;

        $texts = array(
            'This is a test',
            'My name is Matthias',
        );
        $to = 'nl';
        $from = 'en';
        $apiCall = new ApiCall\TranslateArray($texts, $to, $from);

        $translatedTexts = $apiCall->parseResponse($response);
        $this->assertSame(array(
            'Dit is een test',
            'Mijn naam is Matthias',
        ), $translatedTexts);
    }
}
