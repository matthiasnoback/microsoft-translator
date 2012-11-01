<?php

namespace MatthiasNoback\Tests\MicrosoftTranslator;

use MatthiasNoback\MicrosoftTranslator\MicrosoftTranslator;

class MicrosoftTranslatorTest extends \PHPUnit_Framework_TestCase
{
    public function testTranslate()
    {

    }

    public function testTranslateArray()
    {
        $browser = $this->createMockBrowser();
        $accessTokenProvider = $this->createMockAccessTokenProvider();
        $translator = new MicrosoftTranslator($browser, $accessTokenProvider);

        $texts = array(
            'This is a test',
            'My name is Matthias',
        );

        $result = $translator->translateArray($texts, 'nl', 'en');
    }

    private function createMockBrowser()
    {
        return $this
            ->getMockBuilder('Buzz\\Browser')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function createMockAccessTokenProvider()
    {
        return $this->getMock('MatthiasNoback\MicrosoftOAuth\AccessTokenProviderInterface');
    }
}
