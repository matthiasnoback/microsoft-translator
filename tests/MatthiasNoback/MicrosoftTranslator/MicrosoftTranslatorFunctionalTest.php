<?php

namespace MatthiasNoback\Tests\MicrosoftTranslator;

use Buzz\Browser;
use MatthiasNoback\MicrosoftOAuth\AccessTokenProvider;
use MatthiasNoback\MicrosoftTranslator\MicrosoftTranslator;
use MatthiasNoback\MicrosoftOAuth\AccessTokenCache;
use Doctrine\Common\Cache\ArrayCache;

class MicrosoftTranslatorFunctionalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MicrosoftTranslator
     */
    private $translator;

    protected function setUp()
    {
        $browser = new Browser();

        $clientId = $this->getEnvironmentVariable('MICROSOFT_OAUTH_CLIENT_ID');
        $clientSecret = $this->getEnvironmentVariable('MICROSOFT_OAUTH_CLIENT_SECRET');

        $cache = new ArrayCache();
        $accessTokenCache = new AccessTokenCache($cache);
        $accessTokenProvider = new AccessTokenProvider($browser, $clientId, $clientSecret);
        $accessTokenProvider->setCache($accessTokenCache);

        $this->translator = new MicrosoftTranslator($browser, $accessTokenProvider);
    }

    public function testTranslate()
    {
        $translated = $this->translator->translate('This is a test', 'nl', 'en');

        $this->assertSame('Dit is een test', $translated);
    }

    public function testTranslateArray()
    {
        $translatedTexts = $this->translator->translateArray(array(
            'This is a test',
            'My name is Matthias',
        ), 'nl', 'en');

        $this->assertSame(array(
            'Dit is een test',
            'Mijn naam is Matthias'
        ), $translatedTexts);
    }

    private function getEnvironmentVariable($name)
    {
        if (!isset($_ENV[$name])) {
            $this->markTestSkipped(sprintf('Environment variable "%s" is missing', $name));
        }

        return $_ENV[$name];
    }
}
