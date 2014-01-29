<?php

namespace MatthiasNoback\Tests\MicrosoftTranslator\ApiCall;

use MatthiasNoback\MicrosoftTranslator\ApiCall;
use MatthiasNoback\MicrosoftTranslator\ApiCall\Response\TranslationMatch;

class GetTranslationsTest extends \PHPUnit_Framework_TestCase
{
    public function testValidatesLengthOfText()
    {
        $text = str_repeat('t', 10001);

        $this->setExpectedException('\InvalidArgumentException');

        new ApiCall\GetTranslations($text, 'nl');
    }

    public function testPostRequestToGetTranslations()
    {
        $apiCall = new ApiCall\GetTranslations('text', 'nl');

        $this->assertSame('GetTranslations', $apiCall->getApiMethodName());
        $this->assertSame('POST', $apiCall->getHttpMethod());
    }

    public function testQueryParameters()
    {
        $text = 'text';
        $from = 'from';
        $to = 'to';
        $maxTranslations = 4;

        $apiCall = new ApiCall\GetTranslations($text, $to, $from, $maxTranslations);
        $this->assertEquals(array(
            'text'        => $text,
            'from'        => $from,
            'to'          => $to,
            'maxTranslations' => $maxTranslations
        ), $apiCall->getQueryParameters());
    }

    public function testParseResponse()
    {
        $apiCall = new ApiCall\GetTranslations('text', 'nl');

        $response =
<<<EOF
<GetTranslationsResponse xmlns="http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
  <From>en</From>
  <State/>
  <Translations>
    <TranslationMatch>
      <Count>0</Count>
      <MatchDegree>100</MatchDegree>
      <MatchedOriginalText/>
      <Rating>5</Rating>
      <TranslatedText>Dit is een test</TranslatedText>
    </TranslationMatch>
  </Translations>
</GetTranslationsResponse>
EOF;

        $this->assertEquals(array(new TranslationMatch('Dit is een test', 100)), $apiCall->parseResponse($response));
    }
}
